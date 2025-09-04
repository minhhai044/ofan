import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../home/presentation/home_screen.dart';
import '../../pos/presentation/pos_screen.dart';
import '../../products/presentation/products_screen.dart';
import '../../customers/presentation/customers_screen.dart';
import '../../reports/presentation/reports_screen.dart';
import 'app_drawer.dart';

// Navigation provider
final navigationProvider = StateProvider<int>((ref) => 0);

class MainNavigation extends ConsumerWidget {
  const MainNavigation({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final currentIndex = ref.watch(navigationProvider);
    final isDarkMode = ref.watch(themeProvider);

    final screens = [
      const HomeScreen(),
      const PosScreen(),
      const ProductsScreen(),
      const CustomersScreen(),
      const ReportsScreen(),
    ];

    final titles = [
      'Tổng quan',
      'Bán hàng',
      'Sản phẩm',
      'Khách hàng',
      'Báo cáo',
    ];

    return Scaffold(
      drawer: const AppDrawer(),
      appBar: currentIndex == 0 ? AppBar(
        title: Text(titles[currentIndex]),
        actions: [
          IconButton(
            icon: Icon(isDarkMode ? Icons.light_mode : Icons.dark_mode),
            onPressed: () {
              ref.read(themeProvider.notifier).state = !isDarkMode;
            },
          ),
        ],
      ) : null,
      body: IndexedStack(
        index: currentIndex,
        children: screens,
      ),
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        currentIndex: currentIndex,
        onTap: (index) => ref.read(navigationProvider.notifier).state = index,
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.home),
            label: 'Trang chủ',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.point_of_sale),
            label: 'Bán hàng',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.inventory),
            label: 'Sản phẩm',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.people),
            label: 'Khách hàng',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.analytics),
            label: 'Báo cáo',
          ),
        ],
      ),
    );
  }
}
