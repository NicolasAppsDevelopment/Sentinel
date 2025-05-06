#include <WiFi.h>
#include <WiFiManager.h>
#include <HTTPClient.h>

#define D4 15
#define D2 14

void startActionServer();

void setup() {
  Serial.begin(115200);
  Serial.setDebugOutput(true);
  Serial.println();

  pinMode(A0, OUTPUT);
  pinMode(D4, INPUT);
  pinMode(D2, INPUT);
  attachInterrupt(digitalPinToInterrupt(D2), on_reset_btn_pressed, RISING);

  WiFi.setSleep(false);

  Serial.print("WiFi connecting");

  WiFiManager wifiManager;
  wifiManager.setConnectTimeout(20);
  bool res = wifiManager.autoConnect("Module d'action");
  if(!res) {
    Serial.println("Failed to connect");
    ESP.restart();
  }

  Serial.println("");
  Serial.println("WiFi connected");

  startActionServer();

  Serial.print("Action Ready! Use 'http://");
  Serial.print(WiFi.localIP());
  Serial.println("' to connect");

  const String discoverRequestData = "{\"ip\":\"" + WiFi.localIP().toString() + "\",\"mac\":\"" + WiFi.macAddress() + "\",\"type\":\"action\"}";
  postToGateway("devices/discover", discoverRequestData);
}


String postToGateway(String path, String data) {
  String response = "";
  HTTPClient http;
  const String serverUrl = "http://" + WiFi.gatewayIP().toString() + "/" + path;
  http.begin(serverUrl);
  http.addHeader("Content-Type", "application/json");

  const int httpResponseCode = http.POST(data);

  Serial.print("Request sent to ");
  Serial.print(serverUrl);
  Serial.print(", response code is: ");
  Serial.println(httpResponseCode);
  Serial.print("... content: ");
  Serial.println(data);

  if (httpResponseCode>=200 && httpResponseCode<300) {
    response = http.getString();

    Serial.print("... OK response: ");
    Serial.println(response);
  }

  http.end();

  return response;
}

void on_reset_btn_pressed() {
  Serial.println("Reset btn pressed.");
  ESP.restart();
}

void loop() {
  // Do nothing. Everything is done in another task by the web server
  if(digitalRead(D4)) {
    const String triggeredUrl = "devices/triggered";
    const String triggeredRequestData = "{\"ip\":\"" + WiFi.localIP().toString() + "\",\"mac\":\"" + WiFi.macAddress() + "\"}";
    postToGateway(triggeredUrl, triggeredRequestData);
    delay(10000);
  }
}
