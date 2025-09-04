import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ofan_app/core/providers/auth_providers.dart';
import '../../../core/theme/app_theme.dart';
import '../../auth/presentation/user_profile_screen.dart';

class AppDrawer extends ConsumerWidget {
  const AppDrawer({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final user = ref.watch(currentUserProvider);
    final authState = ref.watch(authStateEnhancedProvider);

    return Drawer(
      child: Column(
        children: [
          // User header
          UserAccountsDrawerHeader(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  AppTheme.primaryColor,
                  AppTheme.secondaryColor,
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            currentAccountPicture: CircleAvatar(
              backgroundColor: Colors.white,
              child: user?.avatar != null
                  ? ClipRRect(
                      borderRadius: BorderRadius.circular(30),
                      child: Image.network(
                        user!.avatar!,
                        width: 60,
                        height: 60,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) => Text(
                          user.username!,
                          style: const TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.primaryColor,
                          ),
                        ),
                      ),
                    )
                  : Text(
                      user?.username ?? 'N/A',
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                        color: AppTheme.primaryColor,
                      ),
                    ),
            ),
            accountName: Text(
              user?.username ?? 'Người dùng không xác định',
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
            accountEmail: Text(
              user?.email ?? 'Không có email',
              style: const TextStyle(fontSize: 14),
            ),
          ),

          // Menu items
          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                ListTile(
                  leading: const Icon(Icons.person),
                  title: const Text('Thông tin tài khoản'),
                  onTap: () {
                    Navigator.of(context).pop(); // Close drawer
                    Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (context) => const UserProfileScreen(),
                      ),
                    );
                  },
                ),
                const Divider(),
                ListTile(
                  leading: const Icon(Icons.settings),
                  title: const Text('Cài đặt'),
                  onTap: () {
                    Navigator.of(context).pop();
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Tính năng Cài đặt sẽ sớm có mặt!'),
                      ),
                    );
                  },
                ),
                ListTile(
                  leading: const Icon(Icons.help),
                  title: const Text('Trợ giúp & Hỗ trợ'),
                  onTap: () {
                    Navigator.of(context).pop();
                    _showHelpDialog(context);
                  },
                ),
                ListTile(
                  leading: const Icon(Icons.info),
                  title: const Text('Giới thiệu'),
                  onTap: () {
                    Navigator.of(context).pop();
                    _showAboutDialog(context);
                  },
                ),
              ],
            ),
          ),

          // Logout section
          Container(
            decoration: BoxDecoration(
              border: Border(
                top: BorderSide(color: Colors.grey.shade300),
              ),
            ),
            child: ListTile(
              leading: authState.isLoading
                  ? const SizedBox(
                      width: 24,
                      height: 24,
                      child: CircularProgressIndicator(strokeWidth: 2),
                    )
                  : const Icon(Icons.logout, color: Colors.red),
              title: Text(
                authState.isLoading ? 'Đang đăng xuất...' : 'Đăng xuất',
                style: const TextStyle(color: Colors.red),
              ),
              onTap: authState.isLoading
                  ? null
                  : () => _showLogoutDialog(context, ref),
            ),
          ),
        ],
      ),
    );
  }

  void _showLogoutDialog(BuildContext context, WidgetRef ref) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Đăng xuất'),
        content: const Text('Bạn có chắc muốn đăng xuất?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Hủy'),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.of(context).pop(); // Close dialog
              Navigator.of(context).pop(); // Close drawer
              await ref.read(authStateEnhancedProvider.notifier).logout();
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Đăng xuất'),
          ),
        ],
      ),
    );
  }

  void _showHelpDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Row(
          children: [
            Icon(Icons.help, color: AppTheme.primaryColor),
            SizedBox(width: 8),
            Text('Trợ giúp & Hỗ trợ'),
          ],
        ),
        content: const Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Bạn cần trợ giúp với ứng dụng POS?'),
            SizedBox(height: 16),
            Text('📧 Email: support@posapp.com'),
            SizedBox(height: 8),
            Text('📞 Điện thoại: +1-555-SUPPORT'),
            SizedBox(height: 8),
            Text('🌐 Website: www.posapp.com/help'),
            SizedBox(height: 16),
            Text(
              'Đối với các vấn đề kỹ thuật, vui lòng cung cấp mã người dùng và mô tả sự cố.',
              style: TextStyle(fontSize: 12, color: Colors.grey),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Đóng'),
          ),
        ],
      ),
    );
  }

  void _showAboutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Row(
          children: [
            Icon(Icons.info, color: AppTheme.primaryColor),
            SizedBox(width: 8),
            Text('Giới thiệu Ứng dụng POS'),
          ],
        ),
        content: const Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Mẫu Ứng dụng POS',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 8),
            Text('Phiên bản: 1.0.0'),
            SizedBox(height: 8),
            Text('Xây dựng với Flutter & Riverpod'),
            SizedBox(height: 16),
            Text(
              'Mẫu hệ thống bán hàng đầy đủ tính năng với giao diện hiện đại, quản lý trạng thái và quy trình nghiệp vụ hoàn chỉnh.',
              style: TextStyle(fontSize: 14),
            ),
            SizedBox(height: 16),
            Text(
              '© 2024 Mẫu Ứng dụng POS. Bảo lưu mọi quyền.',
              style: TextStyle(fontSize: 12, color: Colors.grey),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Đóng'),
          ),
        ],
      ),
    );
  }
}
