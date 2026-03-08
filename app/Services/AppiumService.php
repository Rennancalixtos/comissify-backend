<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\Log;

class AppiumService
{
    private string $serverUrl;
    private int    $implicitWait;
    private string $platformName;
    private string $deviceName;
    private string $appPackage;
    private string $appActivity;

    public function __construct()
    {
        $this->serverUrl    = env('APPIUM_SERVER_URL', 'http://localhost:4723');
        $this->implicitWait = (int) env('APPIUM_IMPLICIT_WAIT', 10000);
        $this->platformName = env('APPIUM_PLATFORM_NAME', 'Android');
        $this->deviceName   = env('APPIUM_DEVICE_NAME', '');
        $this->appPackage   = env('APPIUM_APP_PACKAGE', 'com.shopee.id');
        $this->appActivity  = env('APPIUM_APP_ACTIVITY', 'com.shopee.id.MainActivity');
    }

    public function postVideo(
        Account $account,
        string  $videoPath,
        string  $title,
        string  $description,
        string  $affiliateLink,
    ): array {
        // TODO:
        // 1. Build Appium DesiredCapabilities from env config + account session_data.
        //    Use the PHP client: php-webdriver/webdriver or a REST wrapper.
        //
        // 2. Start an Appium session (POST /session).
        //
        // 3. Restore the account's session cookies / auth tokens stored in
        //    $account->session_data so the app starts already logged in.
        //
        // 4. Push video file to device via ADB: adb push {videoPath} /sdcard/DCIM/
        //
        // 5. Use UI Automator locators to:
        //    - Tap the "+" (create) button
        //    - Select "ShopeeVideo"
        //    - Pick the video from the gallery
        //    - Enter $title in the title field
        //    - Enter "{$description}\n{$affiliateLink}" in the description field
        //    - Tap "Post"
        //
        // 6. Wait for the success screen and extract the shopee_video_id from the URL
        //    or accessibility text.
        //
        // 7. Quit the Appium session.
        //
        // 8. Return ['success' => true, 'shopee_video_id' => $id, 'error' => ''].

        Log::info('AppiumService::postVideo called (not yet implemented)', [
            'account'    => $account->shopee_username,
            'videoPath'  => $videoPath,
            'title'      => $title,
        ]);

        return ['success' => false, 'shopee_video_id' => '', 'error' => 'Not implemented'];
    }

    public function isServerAvailable(): bool
    {
        // TODO: GET {serverUrl}/status, return true if HTTP 200.
        return false;
    }

    public function restoreSession(Account $account): bool
    {
        // TODO:
        // 1. Decrypt $account->session_data.
        // 2. Inject cookies/tokens via Appium executeScript or direct API.
        // 3. Verify the app recognises the session (check profile screen).
        return false;
    }


    private function buildCapabilities(): array
    {
        return [
            'platformName'    => $this->platformName,
            'deviceName'      => $this->deviceName,
            'appPackage'      => $this->appPackage,
            'appActivity'     => $this->appActivity,
            'automationName'  => 'UiAutomator2',
            'noReset'         => true,
            'newCommandTimeout' => 300,
        ];
    }
}
