import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../core/providers/product_providers.dart';
import '../../../core/providers/cart_providers.dart';
import '../../../core/models/product.dart';
import 'widgets/product_grid.dart';
import 'widgets/cart_sidebar.dart';
import 'widgets/checkout_dialog.dart';

class PosScreen extends ConsumerStatefulWidget {
  const PosScreen({super.key});

  @override
  ConsumerState<PosScreen> createState() => _PosScreenState();
}

class _PosScreenState extends ConsumerState<PosScreen> {
  final TextEditingController _searchController = TextEditingController();
  final TextEditingController _barcodeController = TextEditingController();

  @override
  void dispose() {
    _searchController.dispose();
    _barcodeController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final products = ref.watch(filteredProductsProvider);
    final categories = ref.watch(categoriesProvider);
    final selectedCategory = ref.watch(selectedCategoryProvider);
    final cart = ref.watch(cartProvider);
    final cartTotal = ref.watch(cartTotalProvider);
    final cartItemsCount = ref.watch(cartItemsCountProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Bán hàng'),
        actions: [
          IconButton(
            icon: Badge(
              label: Text(cartItemsCount.toString()),
              child: const Icon(Icons.shopping_cart),
            ),
            onPressed: () => _showCartSidebar(context),
          ),
        ],
      ),
      body: Row(
        children: [
          // Main content area
          Expanded(
            flex: 3,
            child: Column(
              children: [
                // Search and barcode section
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: TextField(
                              controller: _searchController,
                              decoration: const InputDecoration(
                                hintText: 'Tìm kiếm sản phẩm...',
                                prefixIcon: Icon(Icons.search),
                              ),
                              onChanged: (value) {
                                ref.read(productSearchQueryProvider.notifier).state = value;
                              },
                            ),
                          ),
                          const SizedBox(width: 12),
                          SizedBox(
                            width: 200,
                            child: TextField(
                              controller: _barcodeController,
                              decoration: const InputDecoration(
                                hintText: 'Quét mã vạch...',
                                prefixIcon: Icon(Icons.qr_code_scanner),
                              ),
                              onSubmitted: (barcode) => _scanBarcode(barcode),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      // Category filter
                      categories.when(
                        data: (categoryList) => SizedBox(
                          height: 40,
                          child: ListView.builder(
                            scrollDirection: Axis.horizontal,
                            itemCount: categoryList.length + 1,
                            itemBuilder: (context, index) {
                              if (index == 0) {
                                return Padding(
                                  padding: const EdgeInsets.only(right: 8),
                                  child: FilterChip(
                                    label: const Text('Tất cả'),
                                    selected: selectedCategory == null,
                                    onSelected: (selected) {
                                      ref.read(selectedCategoryProvider.notifier).state = null;
                                    },
                                  ),
                                );
                              }
                              final category = categoryList[index - 1];
                              return Padding(
                                padding: const EdgeInsets.only(right: 8),
                                child: FilterChip(
                                  label: Text(category),
                                  selected: selectedCategory == category,
                                  onSelected: (selected) {
                                    ref.read(selectedCategoryProvider.notifier).state = 
                                        selected ? category : null;
                                  },
                                ),
                              );
                            },
                          ),
                        ),
                        loading: () => const SizedBox.shrink(),
                        error: (_, __) => const SizedBox.shrink(),
                      ),
                    ],
                  ),
                ),
                // Products grid
                Expanded(
                  child: products.when(
                    data: (productList) => ProductGrid(
                      products: productList,
                      onProductTap: _addToCart,
                    ),
                    loading: () => const Center(child: CircularProgressIndicator()),
                    error: (error, _) => Center(
                      child: Text('Lỗi tải sản phẩm: $error'),
                    ),
                  ),
                ),
              ],
            ),
          ),
          // Cart sidebar (visible on larger screens)
          if (MediaQuery.of(context).size.width > 800)
            Container(
              width: 350,
              decoration: BoxDecoration(
                border: Border(
                  left: BorderSide(color: Colors.grey.shade300),
                ),
              ),
              child: const CartSidebar(),
            ),
        ],
      ),
      // Floating checkout button (visible on smaller screens)
      floatingActionButton: MediaQuery.of(context).size.width <= 800 && cart.isNotEmpty
          ? FloatingActionButton.extended(
              onPressed: () => _showCheckoutDialog(context),
              icon: const Icon(Icons.payment),
              label: Text('Thanh toán ${NumberFormat.currency(symbol: '\$').format(cartTotal)}'),
            )
          : null,
    );
  }

  void _addToCart(Product product) {
    if (product.stock > 0) {
      ref.read(cartProvider.notifier).addItem(product);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Đã thêm ${product.name} vào giỏ'),
          duration: const Duration(seconds: 1),
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Sản phẩm đã hết hàng'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  void _scanBarcode(String barcode) {
    if (barcode.isEmpty) return;
    
    ref.read(productByBarcodeProvider(barcode).future).then((product) {
      if (product != null) {
        _addToCart(product);
        _barcodeController.clear();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Không tìm thấy sản phẩm'),
            backgroundColor: Colors.red,
          ),
        );
      }
    });
  }

  void _showCartSidebar(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (context) => DraggableScrollableSheet(
        initialChildSize: 0.7,
        maxChildSize: 0.9,
        minChildSize: 0.5,
        builder: (context, scrollController) => Container(
          decoration: const BoxDecoration(
            borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
          ),
          child: const CartSidebar(),
        ),
      ),
    );
  }

  void _showCheckoutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => const CheckoutDialog(),
    );
  }
}
