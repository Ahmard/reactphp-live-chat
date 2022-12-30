<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use Server\Database\Connection;
use Server\Http\Request;

class SettingsController extends Controller
{

    public function __construct(array $objects)
    {
        parent::__construct($objects);
    }

    public function index(): Response
    {
        return $this->response->view('user/settings/index');
    }

    public function showChangePasswordForm(): Response
    {
        return $this->response->view('user/settings/change-password');
    }

    public function doChangePassword(Request $request): ResponseInterface
    {
        $oldPassword = $request->getParsedBody()['old_password'];
        $newPassword = $request->getParsedBody()['new_password'];
        $confirmPassword = $request->getParsedBody()['confirm_password'];

        $validateOldPass = $this->validatePasswordLength($oldPassword, 'Old password');
        $validateNewPass = $this->validatePasswordLength($newPassword, 'New password');

        if (true !== $validateOldPass) {
            return $validateOldPass;
        }

        if (true !== $validateNewPass) {
            return $validateNewPass;
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->jsonError('New password and confirm password must be same value.');
        }

        $userId = $request->auth()->userId();
        return Connection::get()->query('SELECT password FROM users WHERE id = ?', [$userId])
            ->then(function (Result $result) use ($userId, $newPassword, $oldPassword) {
                //Check if the provided old password match current one
                if (password_verify($oldPassword, $result->rows[0]['password'])) {
                    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    return Connection::get()->query('UPDATE users SET password = ? WHERE id = ?', [$hashedNewPassword, $userId])
                        ->then(function () {
                            return $this->response->jsonSuccessMessage('Password changed successfully.');
                        })
                        ->otherwise(function () {
                            return $this->response->jsonError('Failed to verify old password.');
                        });
                }

                return $this->response->jsonError('Old password is incorrect.');
            })
            ->otherwise(function () {
                return $this->response->jsonError('Failed to change password.');
            });
    }

    /**
     * @param string $password
     * @param string $inputName
     * @return ResponseInterface|bool
     */
    private function validatePasswordLength(string $password, string $inputName): bool|ResponseInterface
    {
        if (strlen($password) < 4) {
            return $this->response->jsonError("{$inputName} length must be at least 4 characters");
        }

        if (strlen($password) > 99) {
            return $this->response->jsonError("{$inputName} length must be lower than 99 characters");
        }

        return true;
    }
}