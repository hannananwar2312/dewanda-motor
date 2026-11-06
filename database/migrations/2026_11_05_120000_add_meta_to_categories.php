<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Category;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'code'))  $table->string('code')->nullable()->after('name');
            if (!Schema::hasColumn('categories', 'emoji')) $table->string('emoji')->nullable()->after('code');
            if (!Schema::hasColumn('categories', 'tint'))  $table->string('tint')->nullable()->after('emoji');
        });

        $map = [
            'Oli'        => ['oli',     '🛢️', 'bg-blue-50'],
            'Ban & Velg' => ['ban',     '🛞', 'bg-zinc-100'],
            'Kampas Rem' => ['rem',     '🛑', 'bg-slate-100'],
            'Busi'       => ['busi',    '⚡', 'bg-orange-50'],
            'Filter'     => ['filter',  '🌀', 'bg-emerald-50'],
            'Lainnya'    => ['lainnya', '📦', 'bg-gray-100'],
        ];
        foreach ($map as $name => [$code, $emoji, $tint]) {
            Category::where('name', $name)->update(['code' => $code, 'emoji' => $emoji, 'tint' => $tint]);
        }

        foreach (Category::whereNull('code')->get() as $c) {
            $c->update([
                'code'  => Str::slug($c->name) ?: ('kat-' . $c->id),
                'emoji' => $c->emoji ?: '📦',
                'tint'  => $c->tint ?: 'bg-gray-100',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['code', 'emoji', 'tint']);
        });
    }
};