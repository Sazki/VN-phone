<?php
// vnpay_config.php - Cấu hình VNPay đã sửa lỗi

class VNPayConfig
{
    // Thông tin demo VNPay - Dành cho test trên localhost
    const VNP_TMN_CODE = "TKGMF6L8"; // Mã demo của VNPay
    const VNP_HASH_SECRET = "OCS1EVZMQXUTMZEG8YTXCJ45HKM7MKF1"; // Secret key demo
    const VNP_URL = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html"; // URL sandbox
    const VNP_RETURN_URL = "http://localhost:90001/VN-Phone/vnpay_return.php"; // Cập nhật đúng port và đường dẫn

    // Lấy IP address của client
    public static function getClientIP()
    {
        // Ưu tiên lấy IP thật trước
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Có thể có nhiều IP, lấy IP đầu tiên
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            // Fallback cho localhost
            $ip = '127.0.0.1';
        }
        
        // Kiểm tra IP hợp lệ
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = '127.0.0.1';
        }
        
        return $ip;
    }

    // Tạo secure hash và URL thanh toán
    public static function createSecureHash($data)
    {
        // Sắp xếp dữ liệu theo key
        ksort($data);
        
        $query = "";
        $hashdata = "";
        $i = 0;
        
        foreach ($data as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo URL
        $vnp_Url = self::VNP_URL . "?" . $query;
        
        // Tạo secure hash
        $vnpSecureHash = hash_hmac('sha512', $hashdata, self::VNP_HASH_SECRET);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    // Xác thực secure hash từ VNPay trả về
    public static function validateSecureHash($inputData)
    {
        if (!isset($inputData['vnp_SecureHash'])) {
            return false;
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, self::VNP_HASH_SECRET);
        return $secureHash === $vnp_SecureHash;
    }
    
    // Thêm hàm tạo mã giao dịch duy nhất
    public static function generateTxnRef($user_id)
    {
        // Tạo mã giao dịch với timestamp và user_id
        return date('YmdHis') . rand(1000, 9999) . '_' . $user_id;
    }
}
?>