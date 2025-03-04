<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_role_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengubah kolom role menjadi enum dengan pilihan admin, siswa, dan bank
            $table->enum('role', ['admin', 'siswa', 'bank'])->default('siswa');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');  // Menghapus kolom role jika migrasi dibatalkan
        });
    }
}
