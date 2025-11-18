<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if users table exists
        if (Schema::hasTable('users')) {
            $connection = DB::connection();
            $driver = $connection->getDriverName();
            
            // For MySQL
            if ($driver === 'mysql') {
                // Check if columns exist using information_schema
                $columns = DB::select("
                    SELECT COLUMN_NAME 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'users'
                ");
                $existingColumns = array_map(function($col) {
                    return $col->COLUMN_NAME;
                }, $columns);
                
                // Add username if it doesn't exist
                if (!in_array('username', $existingColumns)) {
                    try {
                        DB::statement('ALTER TABLE users ADD COLUMN username VARCHAR(255) UNIQUE AFTER id');
                        // Generate usernames for existing users
                        DB::statement("
                            UPDATE users 
                            SET username = LOWER(SUBSTRING_INDEX(email, '@', 1))
                            WHERE username IS NULL OR username = ''
                        ");
                    } catch (\Exception $e) {
                        // If unique constraint fails, add without unique first, then update and add unique
                        DB::statement('ALTER TABLE users ADD COLUMN username VARCHAR(255) AFTER id');
                        DB::statement("
                            UPDATE users 
                            SET username = CONCAT(LOWER(SUBSTRING_INDEX(email, '@', 1)), id)
                            WHERE username IS NULL OR username = ''
                        ");
                        // Make it unique
                        DB::statement('ALTER TABLE users ADD UNIQUE KEY users_username_unique (username)');
                    }
                }
                
                // Rename name to full_name if name exists and full_name doesn't
                if (in_array('name', $existingColumns) && !in_array('full_name', $existingColumns)) {
                    DB::statement('ALTER TABLE users CHANGE name full_name VARCHAR(255)');
                } elseif (!in_array('full_name', $existingColumns)) {
                    DB::statement('ALTER TABLE users ADD COLUMN full_name VARCHAR(255) AFTER email');
                }
                
                // Add role if it doesn't exist
                if (!in_array('role', $existingColumns)) {
                    DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(255) DEFAULT 'student' AFTER password");
                }
                
                // Add is_online if it doesn't exist
                if (!in_array('is_online', $existingColumns)) {
                    DB::statement('ALTER TABLE users ADD COLUMN is_online TINYINT(1) DEFAULT 0 AFTER role');
                }
                
                // Add last_seen if it doesn't exist
                if (!in_array('last_seen', $existingColumns)) {
                    DB::statement('ALTER TABLE users ADD COLUMN last_seen TIMESTAMP NULL AFTER is_online');
                }
                
                // Add avatar_url if it doesn't exist
                if (!in_array('avatar_url', $existingColumns)) {
                    DB::statement('ALTER TABLE users ADD COLUMN avatar_url VARCHAR(255) NULL AFTER full_name');
                }
            } else {
                // For SQLite and other databases, use Schema builder
                Schema::table('users', function (Blueprint $table) {
                    if (!Schema::hasColumn('users', 'username')) {
                        $table->string('username')->unique()->after('id');
                    }
                    if (!Schema::hasColumn('users', 'role')) {
                        $table->string('role')->default('student')->after('password');
                    }
                    if (!Schema::hasColumn('users', 'full_name')) {
                        if (Schema::hasColumn('users', 'name')) {
                            // For SQLite, we can't rename columns easily, so we'll add full_name
                            $table->string('full_name')->nullable()->after('email');
                        } else {
                            $table->string('full_name')->after('email');
                        }
                    }
                    if (!Schema::hasColumn('users', 'is_online')) {
                        $table->boolean('is_online')->default(0)->after('role');
                    }
                    if (!Schema::hasColumn('users', 'last_seen')) {
                        $table->timestamp('last_seen')->nullable()->after('is_online');
                    }
                    if (!Schema::hasColumn('users', 'avatar_url')) {
                        $table->string('avatar_url')->nullable()->after('full_name');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Remove columns if they exist
                if (Schema::hasColumn('users', 'avatar_url')) {
                    $table->dropColumn('avatar_url');
                }
                if (Schema::hasColumn('users', 'last_seen')) {
                    $table->dropColumn('last_seen');
                }
                if (Schema::hasColumn('users', 'is_online')) {
                    $table->dropColumn('is_online');
                }
                if (Schema::hasColumn('users', 'role')) {
                    $table->dropColumn('role');
                }
                if (Schema::hasColumn('users', 'username')) {
                    $table->dropColumn('username');
                }
                // Note: We don't rename full_name back to name in down() to avoid data loss
            });
        }
    }
};
