<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use app\models\QecCommittee;
/**
 * This is the model class for table "users".
 *
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property int|null $department_id
 *
 * @property Attendance[] $attendances
 * @property Departments $department
 * @property Schedule[] $schedules
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{

    /**
     * ENUM field values
     */
    const ROLE_TEACHER = 'teacher';
    const ROLE_CLERK = 'clerk';
    const ROLE_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['department_id'], 'default', 'value' => null],
            [['name', 'email', 'password', 'role'], 'required'],
            [['role'], 'string'],
            [['department_id'], 'integer'],
            [['name', 'email'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 255],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            [['email'], 'unique'],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departments::class, 'targetAttribute' => ['department_id' => 'department_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'role' => 'Role',
            'department_id' => 'Department ID',
        ];
    }

    /**
     * Gets query for [[Attendances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttendances()
    {
        return $this->hasMany(Attendance::class, ['teacher_id' => 'user_id']);
    }

    /**
     * Gets query for [[Department]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Departments::class, ['department_id' => 'department_id']);
    }

    /**
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedule::class, ['teacher_id' => 'user_id']);
    }


    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole()
    {
        return [
            self::ROLE_TEACHER => 'teacher',
            self::ROLE_CLERK => 'clerk',
            self::ROLE_ADMIN => 'admin',
        ];
    }

    /**
     * @return string
     */
    public function displayRole()
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleTeacher()
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function setRoleToTeacher()
    {
        $this->role = self::ROLE_TEACHER;
    }

    /**
     * @return bool
     */
    public function isRoleClerk()
    {
        return $this->role === self::ROLE_CLERK;
    }

    public function setRoleToClerk()
    {
        $this->role = self::ROLE_CLERK;
    }

    /**
     * @return bool
     */
    public function isRoleAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function setRoleToAdmin()
    {
        $this->role = self::ROLE_ADMIN;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }


    public function getRole()
    {
        return $this->role;
    }

    public function isQec()
    {
        return QecCommittee::find()->where(['user_id' => $this->user_id])->one();
    }

    public static function findByEmail($username)
    {
        return static::findOne(['email' => $username]);
    }

    
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
        // return $this->password === $password;
    }
    public static function getRoleRedirects()
    {
        return [
            'admin' => ['default/dashboard'],
            'teacher' => ['site/teacher-dashboard'],
            'clerk' => ['site/clerk-dashboard'],
        ];
    }

    public function getDefaultRedirect()
    {
        $map = self::getRoleRedirects();
        return $map[$this->role] ?? ['default/dashboard'];
    }

    public function getSidebarMenuItems()
    {
        $role = $this->role;
        
        $isQecMember = false;
        if ($this->user_id) {
            $isQecMember = $this->isQec() !== null;
        }

        $effectiveRoles = [];
        
        switch ($role) {
            case self::ROLE_ADMIN:
                $effectiveRoles = ['admin'];
                if ($isQecMember) {
                    $effectiveRoles[] = 'clerk';
                }
                break;
            case self::ROLE_TEACHER:
                $effectiveRoles = ['teacher'];
                if ($isQecMember) {
                    $effectiveRoles[] = 'clerk';
                }
                break;
            case self::ROLE_CLERK:
                $effectiveRoles = ['clerk'];
                break;
            default:
                $effectiveRoles = [];
        }

        $menu = [];

        $allMenuItems = [
            'statistics' => [
                'label' => 'Statistics',
                'items' => [
                    [
                        'label' => 'Teacher Dashboard',
                        'icon' => 'bi bi-person-badge me-2',
                        'url' => ['site/teacher-dashboard'],
                        'roles' => ['teacher'],
                    ],
                    [
                        'label' => 'Clerk Dashboard',
                        'icon' => 'bi bi-building me-2',
                        'url' => ['site/clerk-dashboard'],
                        'roles' => ['clerk'],
                    ],
                    [
                        'label' => 'Attendances',
                        'icon' => 'bi bi-people me-2',
                        'url' => ['attendance/index'],
                        'roles' => ['clerk'],
                    ],
                    [
                        'label' => 'View Schedule',
                        'icon' => 'bi bi-calendar3 me-2',
                        'url' => ['schedule/index'],
                        'roles' => ['teacher'],
                    ],
                    [
                        'label' => 'Attendance Report',
                        'icon' => 'bi bi-file-earmark-pdf me-2',
                        'url' => ['report/index'],
                        'roles' => ['teacher', 'clerk'],
                    ],
                    [
                        'label' => 'Schedule Management',
                        'icon' => 'bi bi-calendar-week me-2',
                        'url' => ['schedule-update/index'],
                        'roles' => ['clerk'],
                    ],
                    [
                        'label' => 'QEC Committee',
                        'icon' => 'bi bi-people-fill',
                        'url' => ['qec-committee/index'],
                        'roles' => ['admin'],
                    ],
                    [
                        'label' => 'User Management',
                        'icon' => 'bi bi-person-gear me-2',
                        'url' => ['admin/index'],
                        'roles' => ['admin'],
                    ],
                ],
            ],
            
        ];
        
        $checkRoles = $effectiveRoles;
        
        foreach ($allMenuItems as $sectionKey => $section) {
            $filteredSectionItems = [];
            foreach ($section['items'] as $item) {
                if (array_intersect($checkRoles, $item['roles'])) {
                    if (isset($item['submenu'])) {
                        $filteredSubmenu = [];
                        foreach ($item['submenu'] as $subItem) {
                            if (array_intersect($checkRoles, $subItem['roles'])) {
                                $filteredSubmenu[] = $subItem;
                            }
                        }

                        if (!empty($filteredSubmenu)) {
                            $item['submenu'] = $filteredSubmenu;
                            unset($item['roles']);
                            $filteredSectionItems[] = $item;
                        }
                    } else {
                        unset($item['roles']);
                        $filteredSectionItems[] = $item;
                    }
                }
            }
            if (!empty($filteredSectionItems)) {
                $menu[] = [
                    'label' => $section['label'],
                    'items' => $filteredSectionItems,
                ];
            }
        }

        return $menu;
    }
}
