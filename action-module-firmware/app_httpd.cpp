#include "esp_http_server.h"
#include <Arduino.h>
#include <WiFi.h>

httpd_handle_t action_httpd = NULL;

static esp_err_t enable_buzzer_handler(httpd_req_t *req) {
  httpd_resp_set_type(req, "application/json");

  digitalWrite(A0, HIGH);

  String response = "{\"success\":\"true\"}";
  return httpd_resp_send(req, response.c_str(), response.length());
}

static esp_err_t disable_buzzer_handler(httpd_req_t *req) {
  httpd_resp_set_type(req, "application/json");

  digitalWrite(A0, LOW);

  String response = "{\"success\":\"true\"}";
  return httpd_resp_send(req, response.c_str(), response.length());
}

static esp_err_t get_status_handler(httpd_req_t *req) {
  httpd_resp_set_type(req, "application/json");

  String response = "{\"rssi\":" + String(WiFi.RSSI()) + ",\"buzzer\":\"" + String(digitalRead(A0) == HIGH ? "on" : "off") + "\"}";
  return httpd_resp_send(req, response.c_str(), response.length());
}

void startActionServer() {
  httpd_config_t config = HTTPD_DEFAULT_CONFIG();
  config.max_uri_handlers = 16;

  httpd_uri_t enable_buzzer_uri = {
    .uri = "/enable_buzzer",
    .method = HTTP_GET,
    .handler = enable_buzzer_handler,
    .user_ctx = NULL
  };

  httpd_uri_t disable_buzzer_uri = {
    .uri = "/disable_buzzer",
    .method = HTTP_GET,
    .handler = disable_buzzer_handler,
    .user_ctx = NULL
  };

    httpd_uri_t get_status_uri = {
      .uri = "/status",
      .method = HTTP_GET,
      .handler = get_status_handler,
      .user_ctx = NULL
    };

  Serial.println("Starting web server on port:");
  Serial.println(config.server_port);
  if (httpd_start(&action_httpd, &config) == ESP_OK) {
    httpd_register_uri_handler(action_httpd, &enable_buzzer_uri);
    httpd_register_uri_handler(action_httpd, &disable_buzzer_uri);
    httpd_register_uri_handler(action_httpd, &get_status_uri);
  }

  config.server_port = 80;
  config.ctrl_port = 80;
}