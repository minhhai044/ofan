import 'user.dart';

class AuthResult {
  final User? user;
  final String? token;

  const AuthResult({
    this.user,
    this.token,
  });

  factory AuthResult.fromJson(Map<String, dynamic> json) {
    return AuthResult(
      user: User.fromJson(json['user']),
      token: json['access_token'],
    );
  }
}

class RegistrationResult {
  final User? user;
  final String? token;
  final String? error;
  final String? verificationRequired;

  const RegistrationResult({
    this.user,
    this.token,
    this.error,
    this.verificationRequired,
  });

  factory RegistrationResult.fromJson(Map<String, dynamic> json) {
    return RegistrationResult(
      user: User.fromJson(json['user']),
      token: json['token'],
      error: json['error'],
      verificationRequired: json['verification_required'],
    );
  }

  factory RegistrationResult.failure(String error) {
    return RegistrationResult(error: error);
  }

  bool get needsVerification => verificationRequired != null;
}

class PasswordResetResult {
  final String? message;
  final String? resetToken;

  const PasswordResetResult({
    this.message,
    this.resetToken,
  });

  factory PasswordResetResult.fromJson(Map<String, dynamic> json) {
    return PasswordResetResult(
      message: json['message'],
      resetToken: json['reset_token'],
    );
  }
}