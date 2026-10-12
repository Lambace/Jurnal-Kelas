public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('no_wa_ortu')->nullable()->after('kelas_id');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('no_wa_ortu');
    });
}