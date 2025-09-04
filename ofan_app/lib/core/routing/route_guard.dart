import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/auth_providers.dart';
import 'app_router.dart';

class RouteGuard extends ConsumerWidget {
  final Widget child;
  final bool requiresAuth;
  final bool requiresEmailVerification;

  const RouteGuard({
    super.key,
    required this.child,
    this.requiresAuth = false,
    this.requiresEmailVerification = false,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authStateEnhancedProvider);

    // Loading state
    if (authState.isLoading) {
      return const Scaffold(
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircularProgressIndicator(),
              SizedBox(height: 16),
              Text('Đang kiểm tra quyền truy cập...'),
            ],
          ),
        ),
      );
    }

    // Check authentication requirement
    if (requiresAuth && !authState.isAuthenticated) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        Navigator.pushReplacementNamed(context, Routes.login);
      });
      return const SizedBox.shrink();
    }

    // Check email verification requirement
    if (requiresEmailVerification && authState.needsEmailVerification) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        Navigator.pushReplacementNamed(context, Routes.emailVerification);
      });
      return const SizedBox.shrink();
    }

    // Redirect authenticated users away from auth screens
    if (authState.isAuthenticated && !authState.needsEmailVerification) {
      final currentRoute = ModalRoute.of(context)?.settings.name;
      if (currentRoute == Routes.login || 
          currentRoute == Routes.register || 
          currentRoute == Routes.emailVerification ||
          currentRoute == Routes.forgotPassword) {
        WidgetsBinding.instance.addPostFrameCallback((_) {
          Navigator.pushReplacementNamed(context, Routes.home);
        });
        return const SizedBox.shrink();
      }
    }

    return child;
  }
}
