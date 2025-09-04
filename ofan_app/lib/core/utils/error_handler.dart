import 'package:flutter/material.dart';
import '../services/api_service.dart';

class ErrorHandler {
  static String getErrorMessage(dynamic error) {
    if (error is ApiException) {
      switch (error.code) {
        case 'NO_INTERNET':
          return 'Không có kết nối internet. Vui lòng kiểm tra lại.';
        case 'TIMEOUT':
          return 'Kết nối timeout. Vui lòng thử lại.';
        case 'HTTP_401':
          return 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.';
        case 'HTTP_403':
          return 'Bạn không có quyền thực hiện hành động này.';
        case 'HTTP_404':
          return 'Không tìm thấy dữ liệu yêu cầu.';
        case 'HTTP_500':
          return 'Lỗi server. Vui lòng thử lại sau.';
        default:
          return error.message;
      }
    }
    
    return 'Đã xảy ra lỗi không xác định. Vui lòng thử lại.';
  }
  
  static void showErrorSnackBar(BuildContext context, dynamic error) {
    final message = getErrorMessage(error);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.red,
        action: SnackBarAction(
          label: 'Đóng',
          textColor: Colors.white,
          onPressed: () => ScaffoldMessenger.of(context).hideCurrentSnackBar(),
        ),
      ),
    );
  }
  
  static Widget buildErrorWidget(dynamic error, VoidCallback? onRetry) {
    final message = getErrorMessage(error);
    
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.error_outline, size: 64, color: Colors.red),
          const SizedBox(height: 16),
          Text(
            message,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 16),
          ),
          if (onRetry != null) ...[
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: onRetry,
              child: const Text('Thử lại'),
            ),
          ],
        ],
      ),
    );
  }
}
