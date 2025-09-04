import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../core/providers/order_providers.dart';
import '../../../core/providers/customer_providers.dart';
import '../../../core/theme/app_theme.dart';
import 'widgets/dashboard_card.dart';
import 'widgets/quick_stats_card.dart';
import 'widgets/recent_orders_card.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final todayRevenue = ref.watch(todayRevenueProvider);
    final todayOrders = ref.watch(todayOrdersCountProvider);
    final totalRevenue = ref.watch(totalRevenueProvider);
    final totalOrders = ref.watch(totalOrdersCountProvider);
    final topCustomers = ref.watch(topCustomersProvider);

    return RefreshIndicator(
        onRefresh: () async {
          ref.invalidate(todayRevenueProvider);
          ref.invalidate(todayOrdersCountProvider);
          ref.invalidate(totalRevenueProvider);
          ref.invalidate(totalOrdersCountProvider);
          ref.invalidate(topCustomersProvider);
        },
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Welcome section
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    children: [
                      const Icon(
                        Icons.waving_hand,
                        size: 32,
                        color: AppTheme.primaryColor,
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Chào mừng quay lại!',
                              style: Theme.of(context).textTheme.headlineSmall,
                            ),
                            Text(
                              'Tổng quan tình hình cửa hàng hôm nay.',
                              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                                color: Colors.grey[600],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // Today's stats
              Text(
                'Hiệu suất hôm nay',
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: todayRevenue.when(
                      data: (revenue) => DashboardCard(
                        title: 'Doanh thu hôm nay',
                        value: NumberFormat.currency(symbol: '\$').format(revenue),
                        icon: Icons.attach_money,
                        color: Colors.green,
                      ),
                      loading: () => const DashboardCard(
                        title: 'Doanh thu hôm nay',
                        value: 'Đang tải...',
                        icon: Icons.attach_money,
                        color: Colors.green,
                      ),
                      error: (error, _) => const DashboardCard(
                        title: 'Doanh thu hôm nay',
                        value: 'Lỗi',
                        icon: Icons.attach_money,
                        color: Colors.green,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: todayOrders.when(
                      data: (orders) => DashboardCard(
                        title: 'Đơn hàng hôm nay',
                        value: orders.toString(),
                        icon: Icons.shopping_cart,
                        color: Colors.blue,
                      ),
                      loading: () => const DashboardCard(
                        title: 'Đơn hàng hôm nay',
                        value: 'Đang tải...',
                        icon: Icons.shopping_cart,
                        color: Colors.blue,
                      ),
                      error: (error, _) => const DashboardCard(
                        title: 'Đơn hàng hôm nay',
                        value: 'Lỗi',
                        icon: Icons.shopping_cart,
                        color: Colors.blue,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              // Overall stats
              Text(
                'Thống kê tổng quan',
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: totalRevenue.when(
                      data: (revenue) => DashboardCard(
                        title: 'Tổng doanh thu',
                        value: NumberFormat.currency(symbol: '\$').format(revenue),
                        icon: Icons.trending_up,
                        color: Colors.purple,
                      ),
                      loading: () => const DashboardCard(
                        title: 'Tổng doanh thu',
                        value: 'Đang tải...',
                        icon: Icons.trending_up,
                        color: Colors.purple,
                      ),
                      error: (error, _) => const DashboardCard(
                        title: 'Tổng doanh thu',
                        value: 'Lỗi',
                        icon: Icons.trending_up,
                        color: Colors.purple,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: totalOrders.when(
                      data: (orders) => DashboardCard(
                        title: 'Tổng số đơn',
                        value: orders.toString(),
                        icon: Icons.receipt_long,
                        color: Colors.orange,
                      ),
                      loading: () => const DashboardCard(
                        title: 'Tổng số đơn',
                        value: 'Đang tải...',
                        icon: Icons.receipt_long,
                        color: Colors.orange,
                      ),
                      error: (error, _) => const DashboardCard(
                        title: 'Tổng số đơn',
                        value: 'Lỗi',
                        icon: Icons.receipt_long,
                        color: Colors.orange,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              // Quick stats
              const QuickStatsCard(),
              const SizedBox(height: 24),

              // Top customers
              topCustomers.when(
                data: (customers) => Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Icon(Icons.star, color: AppTheme.primaryColor),
                            const SizedBox(width: 8),
                            Text(
                              'Khách hàng hàng đầu',
                              style: Theme.of(context).textTheme.titleMedium?.copyWith(
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        ...customers.map((customer) => ListTile(
                          leading: CircleAvatar(
                            backgroundColor: AppTheme.primaryColor,
                            child: Text(
                              customer.name.substring(0, 1).toUpperCase(),
                              style: const TextStyle(color: Colors.white),
                            ),
                          ),
                          title: Text(customer.name),
                          subtitle: Text('${customer.totalOrders} đơn hàng'),
                          trailing: Text(
                            NumberFormat.currency(symbol: '\$').format(customer.totalSpent),
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              color: AppTheme.primaryColor,
                            ),
                          ),
                        )),
                      ],
                    ),
                  ),
                ),
                loading: () => const Card(
                  child: Padding(
                    padding: EdgeInsets.all(16),
                    child: Center(child: CircularProgressIndicator()),
                  ),
                ),
                error: (error, _) => Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Text('Lỗi tải danh sách khách hàng hàng đầu: $error'),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Recent orders
              const RecentOrdersCard(),
            ],
          ),
        ),
      );
  }
}
