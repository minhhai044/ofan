<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Giới hạn hiển thị text
 * 
 * @param string $text  Đoạn text muốn giới hạn
 * @param int    $limit Giới hạn từ muốn hiển thị
 * @param string $end   Chuỗi string sẽ nối vào cuối cùng của chuỗi sau khi giới hạn
 * 
 * @return string Trả về string sau khi đã giới hạn
 */
if (!function_exists('limitText')) {
    function limitText($text, $limit, $end = "...")
    {
        return Str::length($text) > $limit ? Str::limit($text, $limit, $end) : $text;
    }
}

/**
 * Tạo chuỗi id ngẫu nhiên 
 * @return mixed Trả về chuỗi id ngẫu nhiên có định dạng xxx-xxxx-xxx-xxx
 */
if (!function_exists('generateSlugIds')) {
    function generateSlugIds()
    {
        $characters = 'abcdefghjklmnpqrstuvwxyz123456789';
        $charLength = strlen($characters);

        $code = '';
        for ($i = 0; $i < 13; $i++) {
            $code .= $characters[rand(0, $charLength - 1)];
        }

        $formattedCode = substr($code, 0, 3) . '-' .
            substr($code, 3, 4) . '-' .
            substr($code, 7, 3) . '-' .
            substr($code, 10, 3);

        return $formattedCode;
    }
}

/**
 * Tạo chuỗi slug theo name
 * @param string $name Chuỗi name cần tạo slug
 * @return string      Trả về chuỗi slug
 * 
 */

if (!function_exists('generateSlug')) {
    function generateSlug($name)
    {
        return Str::slug($name . '-' . generateSlugIds());
    }
}

/**
 * Tạo thư mục lưu trữ ảnh
 * @param mixed $folder Thư mục lưu trữ trong storage/app/public
 * @param mixed $file   File ảnh cần lưu trữ
 * 
 * @return string       Trả về đường dẫn lưu trữ ảnh
 */


if (!function_exists('createImageStorage')) {
    function createImageStorage($folder, $file)
    {
        return Storage::put($folder, $file);
    }
}


/**
 * Xóa ảnh trong storage
 * 
 * @param mixed $path Đường dẫn ảnh trong storage/app/public
 * 
 * @return bool       Trả về true nếu xóa thành công, ngược lại trả về true
 */

if (!function_exists('deleteImageStorage')) {
    function deleteImageStorage($path)
    {
        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }
        return true;
    }
}

/**
 * Lấy đường dẫn ảnh trong storage
 * 
 * @param mixed $path Đường dẫn ảnh trong storage/app/public
 * 
 * @return string     Trả về đường dẫn ảnh
 */
if (!function_exists('getImageStorage')) {
    function getImageStorage($path)
    {
        if ($path && Storage::exists($path)) {
            return Storage::url($path);
        }
        return asset('no-image.png');
    }
}

/**
 * Tạo mã code misa theo tên và số điện thoại
 * @param string $name  Tên người dùng
 * @param string $phone Số điện thoại người dùng
 * @return string       Trả về mã code misa
 */

if (!function_exists('codeMisa')) {
    function codeMisa($name, $phone)
    {
        $name = Str::ascii($name);

        // Xoá khoảng trắng, ký tự đặc biệt → chỉ giữ A-Z
        $name = strtoupper(preg_replace('/[^A-Za-z]/', '', $name));

        // Ghép với số điện thoại
        return $name . '_' . $phone;
    }
}



/**
 * Tạo key name
 * @param string $name  Tên người dùng
 * @return string Trả về name
 */

if (!function_exists('codeName')) {
    function codeName($name)
    {
        // Bỏ khoảng trắng đầu/cuối
        $name = trim($name);

        // Bỏ dấu tiếng Việt ổn định
        $name = Str::ascii($name);

        // về chữ thường
        $name = mb_strtolower($name, 'UTF-8');

        // Mọi ký tự không phải [a-z0-9_] -> chuyển thành "_"
        $name = preg_replace('/[^\w]+/u', '_', $name);

        // Gom nhiều "_" liên tiếp còn một, và bỏ "_" ở đầu/cuối
        $name = preg_replace('/_+/', '_', trim($name, '_'));

        return $name;
    }
}
