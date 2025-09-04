class RegistrationRequest {
  final String fullName;
  final String email;
  final String phone;
  final String password;
  final String confirmPassword;
  final String? role;
  final String? businessName;
  final String? businessAddress;

  const RegistrationRequest({
    required this.fullName,
    required this.email,
    required this.phone,
    required this.password,
    required this.confirmPassword,
    this.role = 'Staff',
    this.businessName,
    this.businessAddress,
  });

  Map<String, dynamic> toJson() {
    return {
      'fullName': fullName,
      'email': email,
      'phone': phone,
      'password': password,
      'confirmPassword': confirmPassword,
      'role': role,
      'businessName': businessName,
      'businessAddress': businessAddress,
    };
  }

  // Validation methods
  bool get isValid {
    return fullName.isNotEmpty &&
        email.isNotEmpty &&
        phone.isNotEmpty &&
        password.isNotEmpty &&
        password == confirmPassword &&
        isValidEmail &&
        isValidPhone &&
        isValidPassword;
  }

  bool get isValidEmail {
    return RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(email);
  }

  bool get isValidPhone {
    return RegExp(r'^\+?[1-9]\d{1,14}$').hasMatch(phone.replaceAll(RegExp(r'[\s\-\(\)]'), ''));
  }

  bool get isValidPassword {
    return password.length >= 8 &&
        password.contains(RegExp(r'[A-Z]')) && // Uppercase letter
        password.contains(RegExp(r'[a-z]')) && // Lowercase letter
        password.contains(RegExp(r'[0-9]')); // Number
  }

  String? get fullNameError {
    if (fullName.isEmpty) return 'Họ tên không được để trống';
    if (fullName.length < 2) return 'Họ tên phải có ít nhất 2 ký tự';
    return null;
  }

  String? get emailError {
    if (email.isEmpty) return 'Email không được để trống';
    if (!isValidEmail) return 'Email không hợp lệ';
    return null;
  }

  String? get phoneError {
    if (phone.isEmpty) return 'Số điện thoại không được để trống';
    if (!isValidPhone) return 'Số điện thoại không hợp lệ';
    return null;
  }

  String? get passwordError {
    if (password.isEmpty) return 'Mật khẩu không được để trống';
    if (password.length < 8) return 'Mật khẩu phải có ít nhất 8 ký tự';
    if (!password.contains(RegExp(r'[A-Z]'))) return 'Mật khẩu phải có ít nhất 1 chữ hoa';
    if (!password.contains(RegExp(r'[a-z]'))) return 'Mật khẩu phải có ít nhất 1 chữ thường';
    if (!password.contains(RegExp(r'[0-9]'))) return 'Mật khẩu phải có ít nhất 1 số';
    return null;
  }

  String? get confirmPasswordError {
    if (confirmPassword.isEmpty) return 'Xác nhận mật khẩu không được để trống';
    if (password != confirmPassword) return 'Mật khẩu xác nhận không khớp';
    return null;
  }

  RegistrationRequest copyWith({
    String? fullName,
    String? email,
    String? phone,
    String? password,
    String? confirmPassword,
    String? role,
    String? businessName,
    String? businessAddress,
  }) {
    return RegistrationRequest(
      fullName: fullName ?? this.fullName,
      email: email ?? this.email,
      phone: phone ?? this.phone,
      password: password ?? this.password,
      confirmPassword: confirmPassword ?? this.confirmPassword,
      role: role ?? this.role,
      businessName: businessName ?? this.businessName,
      businessAddress: businessAddress ?? this.businessAddress,
    );
  }
}
