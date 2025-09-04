import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'https://1318a6c9c3ec.ngrok-free.app/api';
  
  late final Dio _dio;
  
  ApiService() {
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 30),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));
    
    _setupInterceptors();
  }
  
  void _setupInterceptors() {
    // Request interceptor để thêm token
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _getStoredToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) async {
        // Handle 401 unauthorized - redirect to login
        if (error.response?.statusCode == 401) {
          await _clearStoredToken();
          // Có thể emit event để redirect về login screen
        }
        handler.next(error);
      },
    ));
    
    // Logging interceptor (chỉ trong development)
    _dio.interceptors.add(LogInterceptor(
      requestBody: true,
      responseBody: true,
      // ignore: avoid_print
      logPrint: (object) => print(object),
    ));
  }
  
  Future<String?> _getStoredToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }
  
  Future<void> _clearStoredToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    await prefs.remove('user_data');
  }
  
  // GET request
  Future<Response<T>> get<T>(
    String path, {
    Map<String, dynamic>? queryParameters,
    Options? options,
  }) async {
    try {
      return await _dio.get<T>(
        path,
        queryParameters: queryParameters,
        options: options,
      );
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  // POST request
  Future<Response<T>> post<T>(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
  }) async {
    try {
      return await _dio.post<T>(
        path,
        data: data,
        queryParameters: queryParameters,
        options: options,
      );
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  // PUT request
  Future<Response<T>> put<T>(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
  }) async {
    try {
      return await _dio.put<T>(
        path,
        data: data,
        queryParameters: queryParameters,
        options: options,
      );
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  // DELETE request
  Future<Response<T>> delete<T>(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
  }) async {
    try {
      return await _dio.delete<T>(
        path,
        data: data,
        queryParameters: queryParameters,
        options: options,
      );
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }
  
  // Error handling
  ApiException _handleError(DioException error) {
    switch (error.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        return ApiException('Kết nối timeout. Vui lòng thử lại.', 'TIMEOUT');
      case DioExceptionType.badResponse:
        final statusCode = error.response?.statusCode;
        final responseData = error.response?.data;
        
        // Try to extract error message from response
        String message = 'Lỗi server';
        if (responseData is Map<String, dynamic>) {
          message = responseData['message'] ?? 
                   responseData['error'] ?? 
                   responseData['errors']?.toString() ?? 
                   'Lỗi server';
        } else if (responseData is String) {
          message = responseData;
        }
        
        return ApiException(message, 'HTTP_$statusCode');
      case DioExceptionType.cancel:
        return ApiException('Request bị hủy', 'CANCELLED');
      case DioExceptionType.unknown:
        if (error.message?.contains('SocketException') == true ||
            error.message?.contains('NetworkException') == true) {
          return ApiException('Không có kết nối internet', 'NO_INTERNET');
        }
        return ApiException(
          error.message ?? 'Lỗi không xác định', 
          'UNKNOWN'
        );
      default:
        return ApiException('Lỗi không xác định', 'UNKNOWN');
    }
  }
}

class ApiException implements Exception {
  final String message;
  final String code;
  
  ApiException(this.message, this.code);
  
  @override
  String toString() => 'ApiException: $message (Code: $code)';
}

// API Response wrapper
class ApiResponse<T> {
  final bool success;
  final T? data;
  final String? message;
  final String? error;
  final Map<String, dynamic>? meta;
  
  ApiResponse({
    required this.success,
    this.data,
    this.message,
    this.error,
    this.meta,
  });
  
  factory ApiResponse.fromJson(
    Map<String, dynamic> json,
    T Function(dynamic)? fromJsonT,
  ) {
    return ApiResponse<T>(
      success: json['success'] ?? false,
      data: json['data'] != null && fromJsonT != null 
          ? fromJsonT(json['data']) 
          : json['data'],
      message: json['message'],
      error: json['error'],
      meta: json['meta'],
    );
  }
}
