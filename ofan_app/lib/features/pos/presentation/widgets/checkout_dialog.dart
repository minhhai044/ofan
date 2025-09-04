import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:uuid/uuid.dart';
import '../../../../core/providers/cart_providers.dart';
import '../../../../core/providers/customer_providers.dart';
import '../../../../core/providers/order_providers.dart';
import '../../../../core/models/order.dart';
import '../../../../core/theme/app_theme.dart';

class CheckoutDialog extends ConsumerStatefulWidget {
  const CheckoutDialog({super.key});

  @override
  ConsumerState<CheckoutDialog> createState() => _CheckoutDialogState();
}

class _CheckoutDialogState extends ConsumerState<CheckoutDialog> {
  PaymentMethod _selectedPaymentMethod = PaymentMethod.cash;
  final TextEditingController _notesController = TextEditingController();
  bool _isProcessing = false;

  @override
  void dispose() {
    _notesController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final cart = ref.watch(cartProvider);
    final subtotal = ref.watch(cartSubtotalProvider);
    final discount = ref.watch(cartDiscountProvider);
    final tax = ref.watch(cartTaxProvider);
    final total = ref.watch(cartTotalProvider);
    final selectedCustomer = ref.watch(selectedCustomerProvider);

    return Dialog(
      child: Container(
        width: 500,
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Row(
              children: [
                const Icon(Icons.payment, color: AppTheme.primaryColor),
                const SizedBox(width: 8),
                Text(
                  'Thanh toán',
                  style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const Spacer(),
                IconButton(
                  onPressed: () => Navigator.of(context).pop(),
                  icon: const Icon(Icons.close),
                ),
              ],
            ),
            const SizedBox(height: 24),

            // Order summary
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.grey[50],
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.grey.shade300),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Tóm tắt đơn hàng',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 12),
                  ...cart.map((item) => Padding(
                    padding: const EdgeInsets.symmetric(vertical: 4),
                    child: Row(
                      children: [
                        Text('${item.quantity}x '),
                        Expanded(child: Text(item.product.name)),
                        Text(NumberFormat.currency(symbol: '\$').format(item.totalPrice)),
                      ],
                    ),
                  )),
                  const Divider(),
                  _buildSummaryRow('Tạm tính', subtotal),
                  if (discount > 0) _buildSummaryRow('Giảm giá', -discount),
                  _buildSummaryRow('Thuế', tax),
                  const Divider(),
                  _buildSummaryRow('Tổng cộng', total, isTotal: true),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Customer selection
            Text(
              'Khách hàng',
              style: Theme.of(context).textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                border: Border.all(color: Colors.grey.shade300),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                children: [
                  Icon(Icons.person, color: Colors.grey[600]),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      selectedCustomer?.name ?? 'Khách lẻ',
                      style: Theme.of(context).textTheme.bodyMedium,
                    ),
                  ),
                  TextButton(
                    onPressed: _selectCustomer,
                    child: const Text('Thay đổi'),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Payment method
            Text(
              'Phương thức thanh toán',
              style: Theme.of(context).textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8,
              children: PaymentMethod.values.map((method) {
                return ChoiceChip(
                  label: Text(_getPaymentMethodLabel(method)),
                  selected: _selectedPaymentMethod == method,
                  onSelected: (selected) {
                    if (selected) {
                      setState(() {
                        _selectedPaymentMethod = method;
                      });
                    }
                  },
                );
              }).toList(),
            ),
            const SizedBox(height: 24),

            // Notes
            Text(
              'Ghi chú (Không bắt buộc)',
              style: Theme.of(context).textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: _notesController,
              decoration: const InputDecoration(
                hintText: 'Thêm ghi chú cho đơn hàng này...',
                border: OutlineInputBorder(),
              ),
              maxLines: 3,
            ),
            const SizedBox(height: 32),

            // Action buttons
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: _isProcessing ? null : () => Navigator.of(context).pop(),
                    child: const Text('Hủy'),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton(
                    onPressed: _isProcessing ? null : _processOrder,
                    child: _isProcessing
                        ? const SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(strokeWidth: 2),
                          )
                        : Text('Thanh toán ${NumberFormat.currency(symbol: '\$').format(total)}'),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSummaryRow(String label, double amount, {bool isTotal = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
              fontSize: isTotal ? 16 : 14,
            ),
          ),
          Text(
            NumberFormat.currency(symbol: '\$').format(amount),
            style: TextStyle(
              fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
              fontSize: isTotal ? 16 : 14,
              color: isTotal ? AppTheme.primaryColor : null,
            ),
          ),
        ],
      ),
    );
  }

  String _getPaymentMethodLabel(PaymentMethod method) {
    switch (method) {
      case PaymentMethod.cash:
        return 'Tiền mặt';
      case PaymentMethod.card:
        return 'Thẻ';
      case PaymentMethod.mobile:
        return 'Ví điện tử';
      case PaymentMethod.other:
        return 'Khác';
    }
  }

  void _selectCustomer() {
    // TODO: Implement customer selection dialog
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Tính năng chọn khách hàng sẽ sớm có mặt!'),
      ),
    );
  }

  Future<void> _processOrder() async {
    setState(() {
      _isProcessing = true;
    });

    try {
      final cart = ref.read(cartProvider);
      final subtotal = ref.read(cartSubtotalProvider);
      final discount = ref.read(cartDiscountProvider);
      final tax = ref.read(cartTaxProvider);
      final total = ref.read(cartTotalProvider);
      final selectedCustomer = ref.read(selectedCustomerProvider);

      final order = Order(
        id: const Uuid().v4().substring(0, 8).toUpperCase(),
        items: cart,
        customer: selectedCustomer,
        subtotal: subtotal,
        tax: tax,
        discount: discount,
        total: total,
        status: OrderStatus.completed,
        paymentMethod: _selectedPaymentMethod,
        createdAt: DateTime.now(),
        completedAt: DateTime.now(),
        notes: _notesController.text.isNotEmpty ? _notesController.text : null,
      );

      // Create the order
      await ref.read(orderRepositoryProvider).createOrder(order);

      // Clear the cart
      ref.read(cartProvider.notifier).clearCart();

      // Clear selected customer
      ref.read(selectedCustomerProvider.notifier).state = null;

      // Refresh providers
      ref.invalidate(ordersProvider);
      ref.invalidate(todayRevenueProvider);
      ref.invalidate(todayOrdersCountProvider);
      ref.invalidate(totalRevenueProvider);
      ref.invalidate(totalOrdersCountProvider);

      if (mounted) {
        Navigator.of(context).pop();
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Đơn hàng #${order.id} đã hoàn tất!'),
            backgroundColor: Colors.green,
          ),
        );
      }
    } catch (error) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Lỗi xử lý đơn hàng: $error'),
            backgroundColor: Colors.red,
          ),
        );
      }
    } finally {
      if (mounted) {
        setState(() {
          _isProcessing = false;
        });
      }
    }
  }
}
