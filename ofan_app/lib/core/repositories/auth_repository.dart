import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';

class AuthRepository {
  static const String _tokenKey = 'auth_token';
  static const String _userKey = 'user_data';

  // Mock user data
  static final User _mockUser = User(
    id: 1,
    username: 'John Manager',
    email: 'john.manager@posapp.com',
    phone: '+1-555-0100',
    createdAt: DateTime.now().subtract(const Duration(days: 90)),
  );

  // Mock login credentials
  static const Map<String, String> _mockCredentials = {
    'huynhdepzai@ofan.com': 'password123',
    '+1-555-0100': 'password123',
    'admin@posapp.com': 'admin123',
    'demo@posapp.com': 'demo123',
  };

  Future<String?> getStoredToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  Future<User?> getStoredUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(_userKey);
    if (userJson != null) {
      try {
        final Map<String, dynamic> userData = {};
        // Simple JSON parsing for mock data
        final parts = userJson.split('|');
        if (parts.length >= 6) {
          userData['id'] = parts[0];
          userData['fullName'] = parts[1];
          userData['email'] = parts[2];
          userData['phone'] = parts[3];
          userData['role'] = parts[4];
          userData['createdAt'] = parts[5];
          return User.fromJson(userData);
        }
      } catch (e) {
        // If parsing fails, return null
      }
    }
    return null;
  }

  Future<LoginResult> login(String emailOrPhone, String password) async {
    // Simulate API delay
    await Future.delayed(const Duration(seconds: 2));

    // Check mock credentials
    if (_mockCredentials.containsKey(emailOrPhone) &&
        _mockCredentials[emailOrPhone] == password) {
      
      // Generate mock token
      final token = 'mock_token_${DateTime.now().millisecondsSinceEpoch}';
      
      // Store token and user data
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_tokenKey, token);
      
      // Simple serialization for mock user
      final userString = '${_mockUser.id}|${_mockUser.username}|${_mockUser.email}|${_mockUser.phone}|${_mockUser.role}|${_mockUser.createdAt!.toIso8601String()}';
      await prefs.setString(_userKey, userString);

      return LoginResult(success: true, user: _mockUser, token: token);
    } else {
      return const LoginResult(
        success: false,
        error: 'Invalid email/phone or password',
      );
    }
  }

  Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_userKey);
  }

  Future<User> updateProfile(User updatedUser) async {
    // Simulate API delay
    await Future.delayed(const Duration(milliseconds: 800));
    
    // Store updated user data
    final prefs = await SharedPreferences.getInstance();
    final userString = '${updatedUser.id}|${updatedUser.username}|${updatedUser.email}|${updatedUser.phone}|${updatedUser.role}|${updatedUser.createdAt!.toIso8601String()}';
    await prefs.setString(_userKey, userString);
    
    return updatedUser;
  }

  Future<bool> isLoggedIn() async {
    final token = await getStoredToken();
    return token != null && token.isNotEmpty;
  }
}

class LoginResult {
  final bool success;
  final User? user;
  final String? token;
  final String? error;

  const LoginResult({
    required this.success,
    this.user,
    this.token,
    this.error,
  });
}
