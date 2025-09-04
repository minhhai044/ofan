import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import '../models/registration_request.dart';
import '../models/auth_result.dart';
import '../services/api_service.dart';
import '../config/api_config.dart';
import 'dart:convert';

class AuthRepositoryEnhanced {
  final ApiService _apiService;
  static const String _tokenKey = 'auth_token';
  static const String _refreshTokenKey = 'refresh_token';
  static const String _userKey = 'user_data';

  AuthRepositoryEnhanced(this._apiService);

  // ============ STORAGE METHODS ============
  Future<String?> getStoredToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  Future<String?> getStoredRefreshToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_refreshTokenKey);
  }

  Future<User?> getStoredUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(_userKey);
    if (userJson != null) {
      try {
        final Map<String, dynamic> userData = json.decode(userJson);
        return User.fromJson(userData);
      } catch (e) {
        return null;
      }
    }
    return null;
  }

  Future<void> _storeAuthData({
    required String token,
    required User user,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
    await prefs.setString(_userKey, json.encode(user.toJson()));
  }

  Future<void> _clearAuthData() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_refreshTokenKey);
    await prefs.remove(_userKey);
  }

  // ============ AUTHENTICATION METHODS ============
  Future<AuthResult> login(String emailOrPhone, String password) async {
    try {
      final response = await _apiService.post(
        ApiConfig.login,
        data: {'phone': emailOrPhone, 'password': password},
      );

      // Parse với generic ApiResponse
      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => AuthResult.fromJson(data),
      );

      if (apiResponse.success && apiResponse.data != null) {
        final loginData = apiResponse.data;

        // Store auth data
        await _storeAuthData(
          token: loginData!.token!,
          user: loginData.user!,
        );

        return loginData;
      } else {
        throw Exception(apiResponse.error ?? 'Đăng nhập thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<RegistrationResult> register(RegistrationRequest request) async {
    try {
      final response = await _apiService.post(
        ApiConfig.register,
        data: request.toJson(),
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => RegistrationResult.fromJson(data),
      );

      if (apiResponse.success && apiResponse.data != null) {
        final regData = apiResponse.data!;
        
        // Chỉ lưu auth data nếu không cần verify
        if (regData.token != null && !regData.needsVerification) {
          await _storeAuthData(
            token: regData.token!,
            user: regData.user!,
          );
        }
        
        return regData;
      } else {
        throw Exception(apiResponse.error ?? 'Đăng ký thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<AuthResult> verifyEmail(String email, String verificationCode) async {
    try {
      final response = await _apiService.post(
        ApiConfig.verifyEmail,
        data: {
          'email': email,
          'verification_code': verificationCode,
        },
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => AuthResult.fromJson(data),
      );

      if (apiResponse.success && apiResponse.data != null) {
        final loginData = apiResponse.data!;
        
        await _storeAuthData(
          token: loginData.token!,
          user: loginData.user!,
        );
        
        return loginData;
      } else {
        throw Exception(apiResponse.error ?? 'Xác thực email thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }
  Future<bool> resendVerification(String email) async {
    try {
      final response = await _apiService.post(
        ApiConfig.resendVerification,
        data: {'email': email},
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);
      return apiResponse.success;
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<void> logout() async {
    try {
      await _apiService.post(ApiConfig.logout);
    } catch (e) {
      // Log error but continue with local cleanup
      if (kDebugMode) {
        print('Logout API call failed: $e');
      }
    } finally {
      await _clearAuthData();
    }
  }

  // ============ PASSWORD METHODS ============
  Future<void> forgotPassword(String email) async {
    try {
      final response = await _apiService.post(
        ApiConfig.forgotPassword,
        data: {'email': email},
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => PasswordResetResult.fromJson(data),
      );

      if (!apiResponse.success) {
        throw Exception(apiResponse.error ?? 'Gửi email reset thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<PasswordResetResult> resetPassword({
    required String email,
    required String resetToken,
    required String newPassword,
  }) async {
    try {
      final response = await _apiService.post(
        ApiConfig.resetPassword,
        data: {
          'email': email,
          'reset_token': resetToken,
          'new_password': newPassword,
        },
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);

      if (apiResponse.success) {
        return PasswordResetResult(
          message: apiResponse.message ?? 'Đặt lại mật khẩu thành công',
        );
      } else {
        throw Exception(apiResponse.error ?? 'Đặt lại mật khẩu thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<bool> changePassword({
    required String currentPassword,
    required String newPassword,
  }) async {
    try {
      final response = await _apiService.post(
        ApiConfig.changePassword,
        data: {
          'current_password': currentPassword,
          'new_password': newPassword,
        },
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);
      return apiResponse.success;
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  // ============ PROFILE METHODS ============
  Future<User?> getCurrentUserProfile() async {
    try {
      final response = await _apiService.get(ApiConfig.profile);

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) {
          if (data is Map<String, dynamic> && data.containsKey('user')) {
            return User.fromJson(data['user'] as Map<String, dynamic>);
          } else {
            return User.fromJson(data as Map<String, dynamic>);
          }
        },
      );

      if (apiResponse.success && apiResponse.data != null) {
        // Update stored user data with fresh data
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(
          _userKey,
          json.encode(apiResponse.data!.toJson()),
        );

        return apiResponse.data!;
      } else {
        throw Exception(apiResponse.error ?? 'Lấy thông tin profile thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<User> updateProfile(User updatedUser) async {
    try {
      final response = await _apiService.put(
        ApiConfig.profile,
        data: updatedUser.toJson(),
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => User.fromJson(data as Map<String, dynamic>),
      );

      if (apiResponse.success && apiResponse.data != null) {
        // Update stored user data
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(
          _userKey,
          json.encode(apiResponse.data!.toJson()),
        );

        return apiResponse.data!;
      } else {
        throw Exception(apiResponse.error ?? 'Cập nhật profile thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  // ============ TOKEN METHODS ============
  Future<bool> isLoggedIn() async {
    final token = await getStoredToken();
    if (token == null || token.isEmpty) return false;

    try {
      final response = await _apiService.get(ApiConfig.profile);
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> refreshToken() async {
    try {
      final currentRefreshToken = await getStoredRefreshToken();
      if (currentRefreshToken == null) return false;

      final response = await _apiService.post(
        ApiConfig.refreshToken,
        data: {'refresh_token': currentRefreshToken},
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);

      if (apiResponse.success && apiResponse.data != null) {
        final data = apiResponse.data as Map<String, dynamic>;
        final newToken = data['token'] as String;
        final newRefreshToken = data['refresh_token'] as String?;

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(_tokenKey, newToken);
        if (newRefreshToken != null) {
          await prefs.setString(_refreshTokenKey, newRefreshToken);
        }

        return true;
      }

      return false;
    } catch (e) {
      return false;
    }
  }

  // ============ VALIDATION METHODS ============
  Future<bool> checkEmailExists(String email) async {
    try {
      final response = await _apiService.get(
        '${ApiConfig.auth}/check-email',
        queryParameters: {'email': email},
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);
      return apiResponse.data?['exists'] ?? false;
    } catch (e) {
      return false;
    }
  }

  Future<bool> checkPhoneExists(String phone) async {
    try {
      final response = await _apiService.get(
        '${ApiConfig.auth}/check-phone',
        queryParameters: {'phone': phone},
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);
      return apiResponse.data?['exists'] ?? false;
    } catch (e) {
      return false;
    }
  }
}
