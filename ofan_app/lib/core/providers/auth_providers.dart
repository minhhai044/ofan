import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ofan_app/core/providers/api_providers.dart';
import '../repositories/auth_repository_api.dart';
import '../models/user.dart';
import '../models/registration_request.dart';
import '../models/auth_result.dart';

// // Current user provider
final currentUserProvider = Provider<User?>((ref) {
  final authState = ref.watch(authStateEnhancedProvider);
  return authState.user;
});

// Auth State
class AuthState {
  final User? user;
  final bool isLoading;
  final bool isAuthenticated;
  final String? error;
  final bool needsEmailVerification;
  final String? pendingVerificationEmail;

  const AuthState({
    this.user,
    this.isLoading = false,
    this.isAuthenticated = false,
    this.error,
    this.needsEmailVerification = false,
    this.pendingVerificationEmail,
  });

  AuthState copyWith({
    User? user,
    bool? isLoading,
    bool? isAuthenticated,
    String? error,
    bool? needsEmailVerification,
    String? pendingVerificationEmail,
  }) {
    return AuthState(
      user: user ?? this.user,
      isLoading: isLoading ?? this.isLoading,
      isAuthenticated: isAuthenticated ?? this.isAuthenticated,
      error: error,
      needsEmailVerification:
          needsEmailVerification ?? this.needsEmailVerification,
      pendingVerificationEmail:
          pendingVerificationEmail ?? this.pendingVerificationEmail,
    );
  }
}

// Enhanced Auth State Notifier
class AuthNotifier extends StateNotifier<AuthState> {
  final AuthRepositoryEnhanced _repository;

  AuthNotifier(this._repository) : super(const AuthState()) {
    _checkAuthStatus();
  }

  Future<void> _checkAuthStatus() async {
    state = state.copyWith(isLoading: true);

    try {
      final isLoggedIn = await _repository.isLoggedIn();
      if (isLoggedIn) {
        final user = await _repository.getStoredUser();
        state = state.copyWith(
          user: user,
          isAuthenticated: user != null,
          isLoading: false,
        );
      } else {
        state = state.copyWith(isAuthenticated: false, isLoading: false);
      }
    } catch (e) {
      state = state.copyWith(
        isAuthenticated: false,
        isLoading: false,
        error: 'Lỗi kiểm tra trạng thái đăng nhập',
      );
    }
  }

  Future<bool> login(String emailOrPhone, String password) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      final result = await _repository.login(emailOrPhone, password);

      state = state.copyWith(
        user: result.user,
        isAuthenticated: true,
        isLoading: false,
      );
      return true;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return false;
    }
  }

  Future<RegistrationResult> register(RegistrationRequest request) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      final result = await _repository.register(request);

      if (result.needsVerification) {
        // Cần xác thực email
        state = state.copyWith(
          isLoading: false,
          needsEmailVerification: true,
          pendingVerificationEmail: request.email,
        );
      } else if (result.user != null) {
        // Đăng ký thành công và đã đăng nhập
        state = state.copyWith(
          user: result.user,
          isAuthenticated: true,
          isLoading: false,
        );
      }

      return result;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return RegistrationResult.failure(e.toString());
    }
  }

  Future<bool> verifyEmail(String verificationCode) async {
    if (state.pendingVerificationEmail == null) return false;

    state = state.copyWith(isLoading: true, error: null);

    try {
      final result = await _repository.verifyEmail(
        state.pendingVerificationEmail!,
        verificationCode,
      );

      state = state.copyWith(
        user: result.user,
        isAuthenticated: true,
        isLoading: false,
        needsEmailVerification: false,
        pendingVerificationEmail: null,
      );
      return true;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return false;
    }
  }

  Future<bool> resendVerification() async {
    if (state.pendingVerificationEmail == null) return false;

    try {
      return await _repository.resendVerification(
        state.pendingVerificationEmail!,
      );
    } catch (e) {
      state = state.copyWith(error: e.toString());
      return false;
    }
  }

  Future<void> logout() async {
    state = state.copyWith(isLoading: true);

    try {
      await _repository.logout();
      state = const AuthState();
    } catch (e) {
      // Even if logout fails, clear local state
      state = const AuthState(error: 'Lỗi đăng xuất');
    }
  }

  Future<bool> updateProfile(User updatedUser) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      final user = await _repository.updateProfile(updatedUser);
      state = state.copyWith(user: user, isLoading: false);
      return true;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return false;
    }
  }

  Future<bool> refreshUserProfile() async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      // Check if user is still logged in
      final isLoggedIn = await _repository.isLoggedIn();
      if (!isLoggedIn) {
        state = state.copyWith(
          user: null,
          isAuthenticated: false,
          isLoading: false,
          error: 'Phiên đăng nhập đã hết hạn',
        );
        return false;
      }

      // Fetch fresh user data
      final user = await _repository.getCurrentUserProfile();
      if (user != null) {
        state = state.copyWith(
          user: user,
          isAuthenticated: true,
          isLoading: false,
        );
        return true;
      } else {
        state = state.copyWith(
          isLoading: false,
          error: 'Không thể lấy thông tin người dùng',
        );
        return false;
      }
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return false;
    }
  }

  void clearError() {
    state = state.copyWith(error: null);
  }
}

// Enhanced Auth State Provider
final authStateEnhancedProvider =
    StateNotifierProvider<AuthNotifier, AuthState>((ref) {
      final repository = ref.watch(authRepositoryEnhancedProvider);
      return AuthNotifier(repository);
    });

// Registration Form State
class RegistrationFormState {
  final RegistrationRequest request;
  final Map<String, String?> errors;
  final bool isValidating;

  const RegistrationFormState({
    required this.request,
    this.errors = const {},
    this.isValidating = false,
  });

  RegistrationFormState copyWith({
    RegistrationRequest? request,
    Map<String, String?>? errors,
    bool? isValidating,
  }) {
    return RegistrationFormState(
      request: request ?? this.request,
      errors: errors ?? this.errors,
      isValidating: isValidating ?? this.isValidating,
    );
  }

  bool get isValid => errors.isEmpty && request.isValid;
}

// Registration Form Notifier
class RegistrationFormNotifier extends StateNotifier<RegistrationFormState> {
  final AuthRepositoryEnhanced _repository;

  RegistrationFormNotifier(this._repository)
    : super(
        RegistrationFormState(
          request: const RegistrationRequest(
            fullName: '',
            email: '',
            phone: '',
            password: '',
            confirmPassword: '',
          ),
        ),
      );

  void updateFullName(String fullName) {
    final newRequest = state.request.copyWith(fullName: fullName);
    _updateRequest(newRequest);
  }

  void updateEmail(String email) {
    final newRequest = state.request.copyWith(email: email);
    _updateRequest(newRequest);
    _validateEmailAsync(email);
  }

  void updatePhone(String phone) {
    final newRequest = state.request.copyWith(phone: phone);
    _updateRequest(newRequest);
    _validatePhoneAsync(phone);
  }

  void updatePassword(String password) {
    final newRequest = state.request.copyWith(password: password);
    _updateRequest(newRequest);
  }

  void updateConfirmPassword(String confirmPassword) {
    final newRequest = state.request.copyWith(confirmPassword: confirmPassword);
    _updateRequest(newRequest);
  }

  void updateRole(String role) {
    final newRequest = state.request.copyWith(role: role);
    _updateRequest(newRequest);
  }

  void _updateRequest(RegistrationRequest newRequest) {
    final errors = <String, String?>{};

    // Validate all fields
    errors['fullName'] = newRequest.fullNameError;
    errors['email'] = newRequest.emailError;
    errors['phone'] = newRequest.phoneError;
    errors['password'] = newRequest.passwordError;
    errors['confirmPassword'] = newRequest.confirmPasswordError;

    // Remove null errors
    errors.removeWhere((key, value) => value == null);

    state = state.copyWith(request: newRequest, errors: errors);
  }

  Future<void> _validateEmailAsync(String email) async {
    if (email.isEmpty || !state.request.isValidEmail) return;

    state = state.copyWith(isValidating: true);

    try {
      final exists = await _repository.checkEmailExists(email);
      final currentErrors = Map<String, String?>.from(state.errors);

      if (exists) {
        currentErrors['email'] = 'Email này đã được sử dụng';
      } else {
        currentErrors.remove('email');
      }

      state = state.copyWith(errors: currentErrors, isValidating: false);
    } catch (e) {
      state = state.copyWith(isValidating: false);
    }
  }

  Future<void> _validatePhoneAsync(String phone) async {
    if (phone.isEmpty || !state.request.isValidPhone) return;

    state = state.copyWith(isValidating: true);

    try {
      final exists = await _repository.checkPhoneExists(phone);
      final currentErrors = Map<String, String?>.from(state.errors);

      if (exists) {
        currentErrors['phone'] = 'Số điện thoại này đã được sử dụng';
      } else {
        currentErrors.remove('phone');
      }

      state = state.copyWith(errors: currentErrors, isValidating: false);
    } catch (e) {
      state = state.copyWith(isValidating: false);
    }
  }

  void reset() {
    state = RegistrationFormState(
      request: const RegistrationRequest(
        fullName: '',
        email: '',
        phone: '',
        password: '',
        confirmPassword: '',
      ),
    );
  }
}

// Registration Form Provider
final registrationFormProvider =
    StateNotifierProvider<RegistrationFormNotifier, RegistrationFormState>((
      ref,
    ) {
      final repository = ref.watch(authRepositoryEnhancedProvider);
      return RegistrationFormNotifier(repository);
    });

// Password Reset State
class PasswordResetState {
  final bool isLoading;
  final String? error;
  final String? successMessage;
  final bool emailSent;

  const PasswordResetState({
    this.isLoading = false,
    this.error,
    this.successMessage,
    this.emailSent = false,
  });

  PasswordResetState copyWith({
    bool? isLoading,
    String? error,
    String? successMessage,
    bool? emailSent,
  }) {
    return PasswordResetState(
      isLoading: isLoading ?? this.isLoading,
      error: error,
      successMessage: successMessage,
      emailSent: emailSent ?? this.emailSent,
    );
  }
}

// Password Reset Notifier
class PasswordResetNotifier extends StateNotifier<PasswordResetState> {
  final AuthRepositoryEnhanced _repository;

  PasswordResetNotifier(this._repository) : super(const PasswordResetState());

  Future<bool> forgotPassword(String email) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      await _repository.forgotPassword(email);
      
      state = state.copyWith(
        isLoading: false,
        emailSent: true,
        successMessage: 'Email đặt lại mật khẩu đã được gửi',
      );
      return true;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return false;
    }
  }

  Future<bool> resetPassword({
    required String email,
    required String resetToken,
    required String newPassword,
  }) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      await _repository.resetPassword(
        email: email,
        resetToken: resetToken,
        newPassword: newPassword,
      );

      state = state.copyWith(
        isLoading: false,
        successMessage: 'Đặt lại mật khẩu thành công',
      );
      return true;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return false;
    }
  }

  void reset() {
    state = const PasswordResetState();
  }
}

// Password Reset Provider
final passwordResetProvider =
    StateNotifierProvider<PasswordResetNotifier, PasswordResetState>((ref) {
      final repository = ref.watch(authRepositoryEnhancedProvider);
      return PasswordResetNotifier(repository);
    });
