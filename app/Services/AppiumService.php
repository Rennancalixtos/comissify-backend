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

    /**
     * Automate posting a video to Shopee using the Appium WebDriver.
     *
     * This method controls the Shopee mobile app through ADB/Appium to:
     *   1. Launch the Shopee app with the account's session.
     *   2. Navigate to the video creation / ShopeeVideo section.
     *   3. Upload the video file from the device's local storage.
     *   4. Fill in the post title, description, and affiliate link.
     *   5. Submit the post and capture the returned Shopee video ID.
     *
     * @param  Account  $account       The Shopee account to post from.
     * @param  string   $videoPath     Absolute path to the MP4 file on the host/container.
     * @param  string   $title         Post title (≤ 100 chars).
     * @param  string   $description   Post caption / description.
     * @param  string   $affiliateLink Shopee affiliate URL to append to the description.
     * @return array{success: bool, shopee_video_id: string, error: string}
     *
     * @throws \RuntimeException When Appium server is unreachable or session fails.
     */
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

    /**
     * Check whether the Appium server is reachable and responsive.
     *
     * @return bool  True if the /status endpoint returns a 200 response.
     */
    public function isServerAvailable(): bool
    {
        // TODO: GET {serverUrl}/status, return true if HTTP 200.
        return false;
    }

    /**
     * Restore (or create) a Shopee session for the given account.
     *
     * Loads the session_data (cookies/tokens) stored in the Account model
     * and injects them into the current Appium WebDriver session so the app
     * starts in a logged-in state without requiring credentials.
     *
     * @param  Account  $account  The account whose session should be restored.
     * @return bool               True if the session was successfully restored.
     */
    public function restoreSession(Account $account): bool
    {
        // TODO:
        // 1. Decrypt $account->session_data.
        // 2. Inject cookies/tokens via Appium executeScript or direct API.
        // 3. Verify the app recognises the session (check profile screen).
        return false;
    }

    // ─── Private helpers ─────────────────────────────────────────────────

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
