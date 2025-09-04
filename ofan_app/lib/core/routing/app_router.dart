import 'package:flutter/material.dart';
import '../../features/auth/presentation/login_screen.dart';
import '../../features/auth/presentation/registration_screen.dart';
import '../../features/auth/presentation/email_verification_screen.dart';
import '../../features/auth/presentation/forgot_password_screen.dart';
import '../../features/navigation/presentation/main_navigation.dart';
import '../../main.dart';

class AppRouter {
  static const String login = '/login';
  static const String register = '/register';
  static const String emailVerification = '/email-verification';
  static const String forgotPassword = '/forgot-password';
  static const String home = '/home';
  static const String initial = '/';

  static Route<dynamic> generateRoute(RouteSettings settings) {
    switch (settings.name) {
      case initial:
      case login:
        return MaterialPageRoute(
          builder: (_) => const AuthWrapper(),
          settings: settings,
        );
        
      case register:
        return MaterialPageRoute(
          builder: (_) => const RegistrationScreen(),
          settings: settings,
        );
        
      case emailVerification:
        return MaterialPageRoute(
          builder: (_) => const EmailVerificationScreen(),
          settings: settings,
        );
        
      case forgotPassword:
        return MaterialPageRoute(
          builder: (_) => const ForgotPasswordScreen(),
          settings: settings,
        );
        
      case home:
        return MaterialPageRoute(
          builder: (_) => const MainNavigation(),
          settings: settings,
        );
        
      default:
        return MaterialPageRoute(
          builder: (context) => Scaffold(
            appBar: AppBar(title: const Text('Lỗi')),
            body: Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error, size: 64, color: Colors.red),
                  const SizedBox(height: 16),
                  Text('Route không tồn tại: ${settings.name}'),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () => Navigator.pushReplacementNamed(context, login),
                    child: const Text('Về trang đăng nhập'),
                  ),
                ],
              ),
            ),
          ),
        );
    }
  }

  // Named routes map (alternative approach)
  static Map<String, WidgetBuilder> get routes => {
    login: (context) => const LoginScreen(),
    register: (context) => const RegistrationScreen(),
    emailVerification: (context) => const EmailVerificationScreen(),
    forgotPassword: (context) => const ForgotPasswordScreen(),
    home: (context) => const MainNavigation(),
  };
}

// Route names class for better organization
class Routes {
  static const String login = AppRouter.login;
  static const String register = AppRouter.register;
  static const String emailVerification = AppRouter.emailVerification;
  static const String forgotPassword = AppRouter.forgotPassword;
  static const String home = AppRouter.home;
  static const String initial = AppRouter.initial;
}
