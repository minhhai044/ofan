import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/providers/order_providers.dart';
import 'widgets/sales_chart.dart';
import 'widgets/revenue_summary_card.dart';
import 'widgets/best_sellers_card.dart';

class ReportsScreen extends ConsumerWidget {
  const ReportsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final dailySales = ref.watch(dailySalesProvider);
    final totalRevenue = ref.watch(totalRevenueProvider);
    final totalOrders = ref.watch(totalOrdersCountProvider);
    final bestSellingProducts = ref.watch(bestSellingProductsProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Báo cáo & Phân tích'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              ref.invalidate(dailySalesProvider);
              ref.invalidate(totalRevenueProvider);
              ref.invalidate(totalOrdersCountProvider);
              ref.invalidate(bestSellingProductsProvider);
            },
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          ref.invalidate(dailySalesProvider);
          ref.invalidate(totalRevenueProvider);
          ref.invalidate(totalOrdersCountProvider);
          ref.invalidate(bestSellingProductsProvider);
        },
        child: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Revenue summary cards
            Row(
              children: [
                Expanded(
                  child: totalRevenue.when(
                    data: (revenue) => RevenueSummaryCard(
                      title: 'Tổng doanh thu',
                      amount: revenue,
                      icon: Icons.attach_money,
                      color: Colors.green,
                    ),
                    loading: () => const CircularProgressIndicator(),
                    error: (error, stack) => Text('Lỗi: $error'),
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: totalOrders.when(
                    data: (orders) => RevenueSummaryCard(
                      title: 'Tổng đơn hàng',
                      amount: orders.toDouble(),
                      icon: Icons.receipt_long,
                      color: Colors.blue,
                      isCount: true,
                    ),
                    loading: () => const CircularProgressIndicator(),
                    error: (error, stack) => Text('Lỗi: $error'),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 24),
            
            // Sales chart
            dailySales.when(
              data: (sales) => SalesChart(salesData: sales),
              loading: () => const Center(child: CircularProgressIndicator()),
              error: (error, stack) => Center(child: Text('Lỗi: $error')),
            ),
            
            const SizedBox(height: 24),
            
            // Best selling products
            bestSellingProducts.when(
              data: (products) => BestSellersCard(products: products),
              loading: () => const Center(child: CircularProgressIndicator()),
              error: (error, stack) => Center(child: Text('Lỗi: $error')),
            ),
            
            const SizedBox(height: 24),
            
            // Additional metrics
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Chỉ số bổ sung',
                      style: Theme.of(context).textTheme.titleLarge,
                    ),
                    const SizedBox(height: 16),
                    _buildMetricRow('Giá trị đơn hàng trung bình', '\$45.50'),
                    _buildMetricRow('Tỷ lệ giữ chân khách hàng', '78%'),
                    _buildMetricRow('Danh mục hàng đầu', 'Điện tử'),
                    _buildMetricRow('Khung giờ cao điểm', '14:00 - 16:00'),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    ));
  }

  Widget _buildMetricRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(fontSize: 16),
          ),
          Text(
            value,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }
}
