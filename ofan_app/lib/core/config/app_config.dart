class AppConfig {
  // Environment configuration
  static const String environment = String.fromEnvironment('ENV', defaultValue: 'development');
  
  // Feature flags
  static const bool useApiRepositories = bool.fromEnvironment('USE_API', defaultValue: false);
  static const bool enableOfflineMode = bool.fromEnvironment('OFFLINE_MODE', defaultValue: true);
  static const bool enableAnalytics = bool.fromEnvironment('ANALYTICS', defaultValue: false);
  
  // App settings
  static const int cacheExpirationMinutes = 30;
  static const int maxRetryAttempts = 3;
  static const Duration requestTimeout = Duration(seconds: 30);
  
  // Development settings
  static const bool showDebugInfo = bool.fromEnvironment('DEBUG_INFO', defaultValue: false);
  static const bool mockApiDelay = bool.fromEnvironment('MOCK_DELAY', defaultValue: true);
  
  // Business logic
  static const double defaultTaxRate = 0.1; // 10%
  static const String defaultCurrency = 'VND';
  static const int maxCartItems = 100;
  
  // UI settings
  static const int itemsPerPage = 20;
  static const Duration animationDuration = Duration(milliseconds: 300);
  
  static bool get isDevelopment => environment == 'development';
  static bool get isProduction => environment == 'production';
  static bool get isStaging => environment == 'staging';
}