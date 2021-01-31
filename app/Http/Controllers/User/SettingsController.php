<?php


namespace App\Http\Controllers\User;


use App\Core\Database\Connection;
use App\Core\Http\Response\JsonResponse;
use App\Core\Http\Response\ResponseInterface;
use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class SettingsController extends Controller
{
    public function index(): Response
    {
        return view('user/settings/index');
    }

    public function showChangePasswordForm(): Response
    {
        return view('user/settings/change-password');
    }

    /**
     * @return ResponseInterface|PromiseInterface
     */
    public function doChangePassword()
    {
        $oldPassword = request()->getParsedBody()['old_password'];
        $newPassword = request()->getParsedBody()['new_password'];
        $confirmPassword = request()->getParsedBody()['confirm_password'];

        $validateOldPass = $this->validatePasswordLength($oldPassword, 'Old password');
        $validateNewPass = $this->validatePasswordLength($newPassword, 'New password');

        if (true !== $validateOldPass){
            return $validateOldPass;
        }

        if (true !== $validateNewPass){
            return $validateNewPass;
        }

        if ($newPassword !== $confirmPassword){
            return JsonResponse::error('New password and confirm password must be same value.');
        }

        $userId = request()->auth()->userId();
        return Connection::get()->query('SELECT password FROM users WHERE id = ?', [$userId])
            ->then(function (Result $result) use ($userId, $newPassword, $oldPassword){
                //Check if the provided old password match current one
                if (password_verify($oldPassword, $result->rows[0]['password'])){
                    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    return Connection::get()->query('UPDATE users SET password = ? WHERE id = ?', [$hashedNewPassword, $userId])
                        ->then(function () {
                            return JsonResponse::success('Password changed successfully.');
                        })
                        ->otherwise(function () {
                            return JsonResponse::error('Failed to verify old password.');
                        });
                }

                return JsonResponse::error('Old password is incorrect.');
            })
            ->otherwise(function () {
                return JsonResponse::error('Failed to change password.');
            });
    }

    /**
     * @param string $password
     * @param string $inputName
     * @return ResponseInterface|bool
     */
    private function validatePasswordLength(string $password, string $inputName)
    {
        if (strlen($password) < 4){
            return JsonResponse::error("{$inputName} length must be at least 4 characters");
        }

        if (strlen($password) > 99){
            return JsonResponse::error("{$inputName} length must be lower than 99 characters");
        }

        return true;
    }
}