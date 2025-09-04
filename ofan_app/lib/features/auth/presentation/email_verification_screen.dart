import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ofan_app/core/routing/app_router.dart';
import 'dart:async';
import '../../../core/providers/auth_providers.dart';
import '../../../core/theme/app_theme.dart';

class EmailVerificationScreen extends ConsumerStatefulWidget {
  const EmailVerificationScreen({super.key});

  @override
  ConsumerState<EmailVerificationScreen> createState() => _EmailVerificationScreenState();
}

class _EmailVerificationScreenState extends ConsumerState<EmailVerificationScreen> {
  final _codeController = TextEditingController();
  Timer? _resendTimer;
  int _resendCountdown = 0;

  @override
  void initState() {
    super.initState();
    _startResendTimer();
  }

  @override
  void dispose() {
    _codeController.dispose();
    _resendTimer?.cancel();
    super.dispose();
  }

  void _startResendTimer() {
    _resendCountdown = 60;
    _resendTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_resendCountdown > 0) {
        setState(() => _resendCountdown--);
      } else {
        timer.cancel();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authStateEnhancedProvider);

    // Listen for auth state changes
    ref.listen<AuthState>(authStateEnhancedProvider, (previous, next) {
      if (next.error != null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(next.error!),
            backgroundColor: Colors.red,
          ),
        );
      }
      
      if (next.isAuthenticated) {
        Navigator.pushReplacementNamed(context, Routes.home);
      }
    });

    return Scaffold(
      appBar: AppBar(
        title: const Text('Xác thực Email'),
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Icon
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: AppTheme.primaryColor.withValues(alpha: 0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.mark_email_read,
                  size: 64,
                  color: AppTheme.primaryColor,
                ),
              ),
              const SizedBox(height: 32),

              // Title
              Text(
                'Xác thực Email',
                style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                  fontWeight: FontWeight.bold,
                  color: AppTheme.primaryColor,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 16),

              // Description
              Text(
                'Chúng tôi đã gửi mã xác thực đến email:\n${authState.pendingVerificationEmail ?? ""}',
                style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                  color: Colors.grey[600],
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 32),

              // Verification Code Input
              ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 300),
                child: TextFormField(
                  controller: _codeController,
                  keyboardType: TextInputType.number,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 8,
                  ),
                  decoration: const InputDecoration(
                    labelText: 'Mã xác thực',
                    hintText: '000000',
                    counterText: '',
                  ),
                  maxLength: 6,
                  onChanged: (value) {
                    if (value.length == 6) {
                      _handleVerification();
                    }
                  },
                ),
              ),
              const SizedBox(height: 32),

              // Verify Button
              ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 300),
                child: SizedBox(
                  height: 50,
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: authState.isLoading ? null : _handleVerification,
                    child: authState.isLoading
                        ? const SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                            ),
                          )
                        : const Text(
                            'Xác thực',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Resend Code
              if (_resendCountdown > 0)
                Text(
                  'Gửi lại mã sau ${_resendCountdown}s',
                  style: TextStyle(color: Colors.grey[600]),
                )
              else
                TextButton(
                  onPressed: _handleResendCode,
                  child: const Text('Gửi lại mã xác thực'),
                ),
              
              const SizedBox(height: 16),

              // Back to Login
              TextButton(
                onPressed: () {
                  Navigator.pushReplacementNamed(context, Routes.login);
                },
                child: const Text('Quay lại đăng nhập'),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _handleVerification() async {
    if (_codeController.text.length != 6) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Vui lòng nhập đầy đủ 6 số'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    final success = await ref.read(authStateEnhancedProvider.notifier).verifyEmail(
      _codeController.text,
    );

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Xác thực thành công!'),
          backgroundColor: Colors.green,
        ),
      );
    }
  }

  Future<void> _handleResendCode() async {
    final success = await ref.read(authStateEnhancedProvider.notifier).resendVerification();
    
    if (success) {
      _startResendTimer();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Đã gửi lại mã xác thực!'),
          backgroundColor: Colors.green,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Không thể gửi lại mã. Vui lòng thử lại!'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }
}
