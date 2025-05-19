<?php
    namespace App\Filament\Resources;

    use App\Filament\Resources\AssetResource\Pages;
    use App\Filament\Resources\AssetResource\RelationManagers;
    use App\Models\Asset;
    use Filament\Forms;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\SoftDeletingScope;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Components\TextArea;
    use Filament\Forms\Components\FileUpload;
    use Filament\Tables\Columns\TextColumn;
    use Endroid\QrCode\QrCode;
    use Endroid\QrCode\Writer\PngWriter;
    use Filament\Forms\Components\Select;

    class AssetResource extends Resource
    {
        protected static ?string $model = Asset::class;

        protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
        protected static ?string $navigationLabel = 'Asset';
        protected static ?string $navigationGroup = 'Inventory';

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    TextInput::make('name')
                        ->label('Asset Name')
                        ->required(),
                    TextArea::make('serial_number')
                        ->label('Serial Number')
                        ->required(),
                    Select::make('kategori')
                        ->label('Kategori')
                        ->options([
                            'Hardware' => 'Hardware',
                            'Laptop' => 'Laptop',
                            'Aksesoris' => 'Aksesoris',
                        // Tambahkan kategori lainnya sesuai kebutuhan
                        ])
                        ->required(),
                    FileUpload::make('image')
                        ->label('Image')
                        ->image()
                        ->imagePreviewHeight('250') // untuk preview image di form 
                        ->disk('public') // disk yang digunakan untuk menyimpan file
                        ->directory('assets') // folder tempat menyimpan file
                        ->helperText('Gambar akan auto update ')
                        ->required(),
                        // ->preserveFilenames() // untuk menjaga nama file asli
                ]);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    TextColumn::make('name')->sortable()->searchable(),
                    TextColumn::make('serial_number')->limit(50),
                    TextColumn::make('kategori')->sortable(),
                    \Filament\Tables\Columns\ImageColumn::make('image')
                        ->label('Image')
                        ->disk('public')
                        // ->directory('assets')
                        ->height(20)
                        ->width(20),                  
                ])
                ->filters([
                    //
                ])
                ->actions([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                        
                ])
                ->bulkActions([]);  
        }

        public static function getRelations(): array
        {
            return [
                //
            ];
        }

        public static function getPages(): array
        {
            return [
                'index' => Pages\ListAssets::route('/'),
                'create' => Pages\CreateAsset::route('/create'),
                'edit' => Pages\EditAsset::route('/{record}/edit'),
            ];
        }

        public static function afterCreate(Asset $asset): void
        {
            // Generate QR Code after creating the asset
            $qrCode = new QrCode(route('asset.show', $asset->id));
            $writer = new PngWriter();
            $qrCodePath = 'qr-codes/' . $asset->id . '.png';
            $writer->writeFile($qrCode, storage_path('app/public/' . $qrCodePath));

            // Save the QR Code path in the database
            $asset->update(['qr_code_path' => $qrCodePath]);
        }
    }
