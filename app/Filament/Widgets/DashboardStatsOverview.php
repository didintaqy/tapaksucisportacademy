<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Gallery;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Student;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(UserRole::Admin, UserRole::Operator) ?? false;
    }

    protected function getStats(): array
    {
        $start = now()->startOfMonth();
        $income = (float) Payment::paid()->where('paid_at', '>=', $start)->sum('amount');
        $paidCount = Payment::paid()->where('paid_at', '>=', $start)->count();
        $outstanding = (float) Payment::outstanding()->sum('amount');
        $outstandingCount = Payment::outstanding()->count();
        $dueSoon = Payment::outstanding()
            ->where(fn ($query) => $query->whereNull('due_date')->orWhereDate('due_date', '<=', now()->addDays(7)))
            ->count();

        return [
            Stat::make('Total Siswa', (string) Student::count())
                ->description('Seluruh siswa terdaftar')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('success')
                ->url(StudentResource::getUrl()),

            Stat::make('Pemasukan Bulan Ini', 'Rp '.number_format($income, 0, ',', '.'))
                ->description($paidCount.' transaksi lunas')
                ->descriptionIcon('heroicon-m-banknotes', IconPosition::Before)
                ->color('success')
                ->url(PaymentResource::getUrl()),

            Stat::make('Tunggakan', 'Rp '.number_format($outstanding, 0, ',', '.'))
                ->description($outstandingCount.' tagihan belum lunas')
                ->descriptionIcon('heroicon-m-exclamation-triangle', IconPosition::Before)
                ->color('danger')
                ->url(PaymentResource::getUrl()),

            Stat::make('Jatuh Tempo ≤ 7 Hari', (string) $dueSoon)
                ->description('Termasuk tanpa tanggal jatuh tempo')
                ->descriptionIcon('heroicon-m-clock', IconPosition::Before)
                ->color('warning')
                ->url(PaymentResource::getUrl()),

            Stat::make('Berita Draf', (string) Post::query()->where('status', 'draft')->count())
                ->description('Belum naik ke website')
                ->descriptionIcon('heroicon-m-pencil-square', IconPosition::Before)
                ->color('info')
                ->url(PostResource::getUrl()),

            Stat::make('Terbit Bulan Ini', (string) Post::published()
                ->whereBetween('published_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count())
                ->description('Artikel yang sudah tayang')
                ->descriptionIcon('heroicon-m-newspaper', IconPosition::Before)
                ->color('success')
                ->url(PostResource::getUrl()),

            Stat::make('Galeri Belum Terbit', (string) Gallery::query()->where('is_published', false)->count())
                ->description('Belum tampil di website')
                ->descriptionIcon('heroicon-m-photo', IconPosition::Before)
                ->color('gray')
                ->url(GalleryResource::getUrl()),
        ];
    }
}
