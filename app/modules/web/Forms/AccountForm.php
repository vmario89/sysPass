<?php
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2018, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Modules\Web\Forms;

use SP\Account\AccountRequest;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Exceptions\ValidationException;

/**
 * Class AccountForm
 *
 * @package SP\Account
 */
class AccountForm extends FormBase implements FormInterface
{
    /**
     * @var AccountRequest
     */
    protected $accountRequest;

    /**
     * Validar el formulario
     *
     * @param $action
     * @return bool
     * @throws \SP\Core\Exceptions\ValidationException
     */
    public function validate($action)
    {
        switch ($action) {
            case ActionsInterface::ACCOUNT_EDIT_PASS:
                $this->analyzeRequestData();
                $this->checkPass();
                break;
            case ActionsInterface::ACCOUNT_EDIT:
                $this->analyzeRequestData();
                $this->checkCommon();
                break;
            case ActionsInterface::ACCOUNT_CREATE:
            case ActionsInterface::ACCOUNT_COPY:
                $this->analyzeRequestData();
                $this->checkCommon();
                $this->checkPass();
                break;
        }

        return true;
    }

    /**
     * Analizar los datos de la petición HTTP
     *
     * @return void
     */
    protected function analyzeRequestData()
    {
        $this->accountRequest = new AccountRequest();
        $this->accountRequest->id = $this->itemId;
        $this->accountRequest->name = $this->request->analyzeString('name');
        $this->accountRequest->clientId = $this->request->analyzeInt('client_id', 0);
        $this->accountRequest->categoryId = $this->request->analyzeInt('category_id', 0);
        $this->accountRequest->login = $this->request->analyzeString('login');
        $this->accountRequest->url = $this->request->analyzeString('url');
        $this->accountRequest->notes = $this->request->analyzeString('notes');
        $this->accountRequest->userEditId = $this->context->getUserData()->getId();
        $this->accountRequest->otherUserEdit = (int)$this->request->analyzeBool('other_user_edit_enabled', false);
        $this->accountRequest->otherUserGroupEdit = (int)$this->request->analyzeBool('other_usergroup_edit_enabled', false);
        $this->accountRequest->pass = $this->request->analyzeEncrypted('password');
        $this->accountRequest->isPrivate = (int)$this->request->analyzeBool('private_enabled', false);
        $this->accountRequest->isPrivateGroup = (int)$this->request->analyzeBool('private_group_enabled', false);
        $this->accountRequest->passDateChange = $this->request->analyzeInt('password_date_expire_unix');
        $this->accountRequest->parentId = $this->request->analyzeInt('parent_account_id');
        $this->accountRequest->userGroupId = $this->request->analyzeInt('main_usergroup_id');

        // Arrays
        $accountOtherGroupsView = $this->request->analyzeArray('other_usergroups_view');
        $accountOtherGroupsEdit = $this->request->analyzeArray('other_usergroups_edit');
        $accountOtherUsersView = $this->request->analyzeArray('other_users_view');
        $accountOtherUsersEdit = $this->request->analyzeArray('other_users_edit');
        $accountTags = $this->request->analyzeArray('tags');

        $this->accountRequest->updateUserGroupPermissions = $this->request->analyzeInt('other_usergroups_view_update') === 1 || $this->request->analyzeInt('other_usergroups_edit_update') === 1;
        $this->accountRequest->updateUserPermissions = $this->request->analyzeInt('other_users_view_update') === 1 || $this->request->analyzeInt('other_users_edit_update') === 1;
        $this->accountRequest->updateTags = $this->request->analyzeInt('tags_update') === 1;

        if ($accountOtherUsersView) {
            $this->accountRequest->usersView = $accountOtherUsersView;
        }
        if ($accountOtherUsersEdit) {
            $this->accountRequest->usersEdit = $accountOtherUsersEdit;
        }

        if ($accountOtherGroupsView) {
            $this->accountRequest->userGroupsView = $accountOtherGroupsView;
        }

        if ($accountOtherGroupsEdit) {
            $this->accountRequest->userGroupsEdit = $accountOtherGroupsEdit;
        }

        if ($accountTags) {
            $this->accountRequest->tags = $accountTags;
        }
    }

    /**
     * @throws ValidationException
     */
    protected function checkPass()
    {
        if ($this->accountRequest->parentId > 0) {
            return;
        }

        if (!$this->accountRequest->pass) {
            throw new ValidationException(__u('Es necesaria una clave'));
        }

        if ($this->request->analyzeEncrypted('password_repeat') !== $this->accountRequest->pass) {
            throw new ValidationException(__u('Las claves no coinciden'));
        }
    }

    /**
     * @throws ValidationException
     */
    protected function checkCommon()
    {
        if (!$this->accountRequest->name) {
            throw new ValidationException(__u('Es necesario un nombre de cuenta'));
        }

        if (!$this->accountRequest->clientId) {
            throw new ValidationException(__u('Es necesario un nombre de cliente'));
        }

        if (!$this->accountRequest->login) {
            throw new ValidationException(__u('Es necesario un usuario'));
        }

        if (!$this->accountRequest->categoryId) {
            throw new ValidationException(__u('Es necesario una categoría'));
        }
    }

    /**
     * @return AccountRequest
     */
    public function getItemData()
    {
        return $this->accountRequest;
    }
}