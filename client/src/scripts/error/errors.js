import ErrorMessages from "./messages.json";
import ErrorDescriptions from "./descriptions.json";

const readJson = (path) => {};

class ErrorHandling {
  ClientCodes = [
    400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414,
    415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451,
  ];
  ServerCodes = [500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511];

  JSCodes = [
    "ERR_BAD_OPTION_VALUE",
    "ERR_BAD_OPTION",
    "ECONNABORTED",
    "ETIMEDOUT",
    "ERR_NETWORK",
    "ERR_FR_TOO_MANY_REDIRECTS",
    "ERR_DEPRECATED",
    "ERR_BAD_RESPONSE",
    "ERR_BAD_REQUEST",
    "ERR_CANCELED",
    "ERR_NOT_SUPPORT",
    "ERR_INVALID_URL",
  ];
  constructor(response = null) {
    this.response = response;

    this.code = response.status ?? response.code;
    this.error = ErrorDescriptions[this.code];
    this.message = ErrorMessages[this.code];
  }
}

export { ErrorHandling, ErrorMessages, ErrorDescriptions };
