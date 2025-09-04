class ApiConfig {
  // API Base URLs cho các môi trường khác nhau
  static const String devBaseUrl = 'https://1318a6c9c3ec.ngrok-free.app/api';
  static const String stagingBaseUrl = 'https://1318a6c9c3ec.ngrok-free.app/api';
  static const String prodBaseUrl = 'https://1318a6c9c3ec.ngrok-free.app/api';
  
  // Chọn môi trường hiện tại
  static const String currentEnvironment = 'development'; // development, staging, production
  
  static String get baseUrl {
    switch (currentEnvironment) {
      case 'staging':
        return stagingBaseUrl;
      case 'production':
        return prodBaseUrl;
      default:
        return devBaseUrl;
    }
  }
  
  // API Endpoints
  static const String auth = '/auth';
  static const String login = '$auth/login';
  static const String register = '$auth/register';
  static const String logout = '$auth/logout';
  static const String refreshToken = '$auth/refresh';
  static const String profile = '$auth/profile';
  static const String verifyEmail = '$auth/verify-email';
  static const String resendVerification = '$auth/resend-verification';
  static const String forgotPassword = '$auth/forgot-password';
  static const String resetPassword = '$auth/reset-password';
  static const String changePassword = '$auth/change-password';
  
  static const String products = '/products';
  static const String productCategories = '$products/categories';
  static const String productSearch = '$products/search';
  
  static const String customers = '/customers';
  static const String customerSearch = '$customers/search';
  
  static const String orders = '/orders';
  static const String ordersByCustomer = '$orders/customer';
  static const String ordersAnalytics = '$orders/analytics';
  
  // Timeout configurations
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
  static const Duration sendTimeout = Duration(seconds: 30);
  
  // Pagination
  static const int defaultPageSize = 20;
  static const int maxPageSize = 100;
}

// API Response status codes
class ApiStatusCodes {
  static const int success = 200;
  static const int created = 201;
  static const int noContent = 204;
  static const int badRequest = 400;
  static const int unauthorized = 401;
  static const int forbidden = 403;
  static const int notFound = 404;
  static const int conflict = 409;
  static const int unprocessableEntity = 422;
  static const int internalServerError = 500;
  static const int serviceUnavailable = 503;
}
