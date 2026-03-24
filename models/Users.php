<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
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

    public static function findByEmail($username)
    {
        return static::findOne(['email' => $username]);
    }

    public function validatePassword($password)
    {
        // return Yii::$app->security->validatePassword($password, $this->password);
        return $this->password === $password;
    }
    public static function getRoleRedirects()
    {
        return [
            'Super Admin' => ['default/dashboard'],
            'Content Manager' => ['mcq/manage'],
            'Support Team' => ['support/mcq-reports'],
        ];
    }

    /**
     * Returns the default redirect route for the current user's role.
     */
    public function getDefaultRedirect()
    {
        $map = self::getRoleRedirects();
        return $map[$this->role] ?? ['default/dashboard'];
    }

    public function getSidebarMenuItems()
    {
        $role = $this->role;

        $menu = [];

        $allMenuItems = [
            'statistics' => [
                'label' => 'Statistics',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'icon' => 'bi bi-bar-chart-line me-2',
                        'url' => ['site/index'],
                        'roles' => ['teacher', 'clerk'],
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
                ],
            ],
            'subscriptions' => [
                'label' => 'Subscriptions',
                'items' => [
                    [
                        'label' => 'Subscriptions Management',
                        'icon' => 'bi bi-calendar3',
                        'url' => ['subscription/index'],
                        'roles' => ['Super Admin', 'Finance Manager'],
                    ],
                    [
                        'label' => 'Coupons Management',
                        'icon' => 'bi bi-ticket-perforated-fill',
                        'url' => ['coupons/index'],
                        'roles' => ['Super Admin', 'Finance Manager'],
                    ],
                ],
            ],
            'data_entry' => [
                'label' => 'Data Entry',
                'items' => [
                    [
                        'label' => 'MCQ Management',
                        'icon' => 'bi bi-database-fill',
                        'roles' => ['Super Admin', 'Content Manager'],
                        'submenu' => [
                            ['label' => 'Manage MCQs', 'url' => ['mcq/manage'], 'roles' => ['Super Admin', 'Content Manager', 'Support Team']],
                            ['label' => 'Add MCQs', 'url' => ['mcq/add'], 'roles' => ['Super Admin', 'Content Manager']],
                            ['label' => 'Import from File', 'url' => ['mcq/import-mcq'], 'roles' => ['Super Admin', 'Content Manager']],
                        ],
                    ],
                    [
                        'label' => 'Hierarchy Configuration',
                        'icon' => 'bi bi-diagram-3-fill',
                        'roles' => ['Super Admin', 'Content Manager'],
                        'submenu' => [
                            ['label' => 'Manage Hierarchy', 'url' => ['hierarchy/index'], 'roles' => ['Super Admin', 'Content Manager']],
                            ['label' => 'Manage Systems & Subjects', 'url' => ['hierarchy/systems-subjects'], 'roles' => ['Super Admin', 'Content Manager']],
                            ['label' => 'Manage Chapters & Topics', 'url' => ['hierarchy/topics-chapters'], 'roles' => ['Super Admin', 'Content Manager']],
                        ],
                    ],
                ],
            ],
            'exam_management' => [
                'label' => 'Exam Management',
                'items' => [
                    [
                        'label' => 'Exam Configuration',
                        'icon' => 'bi bi-ui-checks-grid',
                        'roles' => ['Super Admin', 'Content Manager'],
                        'submenu' => [
                            ['label' => 'Exam Types & Specialties', 'url' => ['exam/index'], 'roles' => ['Super Admin', 'Content Manager']],
                            ['label' => 'Mock Exam Distribution (NF)', 'url' => ['exam/distribution'], 'roles' => ['Super Admin', 'Content Manager']],
                        ],
                    ],
                ],
            ],
            'partners' => [
                'label' => 'Partners',
                'items' => [
                    [
                        'label' => 'External Partners',
                        'icon' => 'bi bi-building-add',
                        'roles' => ['Super Admin', 'Finance Manager'],
                        'submenu' => [
                            ['label' => 'Add New Partner', 'url' => ['external-partners/create'], 'roles' => ['Super Admin']],
                            ['label' => 'View All Partners', 'url' => ['external-partners/index'], 'roles' => ['Super Admin']],
                        ],
                    ],
                ],
            ],
            'support' => [
                'label' => 'Support',
                'items' => [
                    [
                        'label' => 'MCQ Reports',
                        'icon' => 'bi bi-ticket',
                        'url' => ['support/mcq-reports'],
                        'roles' => ['Super Admin', 'Support Team', 'Content Manager'],
                    ],
                ],
            ],
            'team_management' => [
                'label' => 'Team Management',
                'items' => [
                    [
                        'label' => 'Manage Team',
                        'icon' => 'bi bi-person-gear',
                        'url' => ['default/team-management'],
                        'roles' => ['Super Admin'],
                    ],
                ],
            ],
        ];

        foreach ($allMenuItems as $sectionKey => $section) {
            $filteredSectionItems = [];
            foreach ($section['items'] as $item) {
                if (in_array($role, $item['roles'])) {
                    if (isset($item['submenu'])) {
                        $filteredSubmenu = [];
                        foreach ($item['submenu'] as $subItem) {
                            if (in_array($role, $subItem['roles'])) {
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
