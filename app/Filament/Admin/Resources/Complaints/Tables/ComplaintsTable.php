<?php

namespace App\Filament\Admin\Resources\Complaints\Tables;

use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Models\Complaint;
use App\Models\User;
use App\Services\ComplaintWorkflow;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ComplaintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('complaint_code')->label('Nomor Pengaduan')->searchable()->sortable(),
                TextColumn::make('title')->label('Judul')->searchable()->limit(45),
                TextColumn::make('category.name')->label('Kategori')->searchable()->sortable(),
                TextColumn::make('village')->label('Kelurahan')->searchable(),
                TextColumn::make('district')->label('Kecamatan')->searchable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (ComplaintStatus $state): string => $state->label())
                    ->color(fn (ComplaintStatus $state): string => match ($state) {
                        ComplaintStatus::Pending, ComplaintStatus::Baru => 'warning',
                        ComplaintStatus::Diverifikasi, ComplaintStatus::Diproses => 'info',
                        ComplaintStatus::Selesai => 'success',
                        ComplaintStatus::Ditolak => 'danger',
                    }),
                TextColumn::make('created_at')->label('Dikirim')->dateTime('d M Y, H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(self::statusOptions()),
                SelectFilter::make('category')->label('Kategori')->relationship('category', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('verify')->label('Verifikasi')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Complaint $record): bool => in_array($record->status, [ComplaintStatus::Pending, ComplaintStatus::Baru], true))
                    ->requiresConfirmation()
                    ->action(fn (Complaint $record) => app(ComplaintWorkflow::class)->verify($record, Auth::user())),
                Action::make('reject')->label('Tolak')->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn (Complaint $record): bool => in_array($record->status, [ComplaintStatus::Pending, ComplaintStatus::Baru, ComplaintStatus::Diverifikasi, ComplaintStatus::Diproses], true))
                    ->schema([Textarea::make('reason')->label('Alasan penolakan')->required()->minLength(5)->maxLength(1000)])
                    ->action(fn (Complaint $record, array $data) => app(ComplaintWorkflow::class)->reject($record, Auth::user(), $data['reason'])),
                Action::make('assign')->label('Tugaskan')->icon('heroicon-o-user-plus')
                    ->visible(fn (Complaint $record): bool => $record->activeAssignment === null
                        && ! in_array($record->status, [ComplaintStatus::Selesai, ComplaintStatus::Ditolak], true))
                    ->schema([
                        Select::make('user_id')->label('Petugas')->options(fn (): array => User::query()
                            ->where('role', UserRole::Petugas->value)->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()->required(),
                        Textarea::make('notes')->label('Catatan penugasan')->maxLength(1000),
                    ])
                    ->action(fn (Complaint $record, array $data) => app(ComplaintWorkflow::class)->assign(
                        $record,
                        User::query()->findOrFail($data['user_id']),
                        Auth::user(),
                        $data['notes'] ?? null,
                    )),
                Action::make('updateStatus')->label('Ubah status')->icon('heroicon-o-arrow-path')
                    ->visible(fn (Complaint $record): bool => in_array(ComplaintStatus::Diproses, $record->status->allowedTransitions(), true))
                    ->schema([
                        Select::make('status')->label('Status berikutnya')->options(fn (Complaint $record): array => collect($record->status->allowedTransitions())
                            ->reject(fn (ComplaintStatus $status): bool => in_array($status, [ComplaintStatus::Ditolak, ComplaintStatus::Selesai], true))
                            ->mapWithKeys(fn (ComplaintStatus $status): array => [$status->value => $status->label()])->all())
                            ->required(),
                        Textarea::make('notes')->label('Catatan')->maxLength(1000),
                    ])
                    ->action(fn (Complaint $record, array $data) => app(ComplaintWorkflow::class)->transition(
                        $record,
                        Auth::user(),
                        ComplaintStatus::from($data['status']),
                        $data['notes'] ?? null,
                    )),
                Action::make('complete')->label('Selesaikan')->icon('heroicon-o-check-badge')->color('success')
                    ->visible(fn (Complaint $record): bool => $record->status === ComplaintStatus::Diproses)
                    ->schema([Textarea::make('notes')->label('Catatan penyelesaian')->maxLength(1000)])
                    ->requiresConfirmation()
                    ->action(fn (Complaint $record, array $data) => app(ComplaintWorkflow::class)->complete($record, Auth::user(), $data['notes'] ?? null)),
            ]);
    }

    /** @return array<string, string> */
    private static function statusOptions(): array
    {
        return collect(ComplaintStatus::cases())->mapWithKeys(fn (ComplaintStatus $status): array => [
            $status->value => $status->label(),
        ])->all();
    }
}
