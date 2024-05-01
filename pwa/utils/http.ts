export enum HttpResponse {
  Unauthorized = 401,
  Forbidden = 403,
  UnprocessableEntity = 422,
  NoContent = 424,
  ServerError = 500,
  ServiceUnavailable = 503,
  GatewayTimeout = 504,
}
