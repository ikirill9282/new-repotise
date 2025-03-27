<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionResource\Pages;
use App\Models\Admin\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Illuminate\Support\Facades\Vite;
use Filament\Tables\Grouping\Group;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class SectionResource extends Resource
{
  protected static ?string $model = Section::class;

  // protected static ?string $navigationParentItem = 'Layout';

  protected static ?string $navigationGroup = 'Layouts';

  // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

  public static function form(Form $form): Form
  {
    FilamentAsset::register([
      Css::make('app.css', Vite::useHotFile('admin.hot')
        ->asset('resources/css/app.css', 'build'))
    ]);
    return $form
      ->schema([
        TextInput::make('title')->required(),
        TextInput::make('slug')->required(),
        Select::make('type')
          ->options([
            'site' => 'site',
            'wire' => 'wire',
          ])
          ->required(),
        Select::make('component')
          ->options(function() {
            $path = app()->basePath() . '/resources/views/site/sections';
            $sections = glob("$path/*");
            $options = [];
            foreach ($sections as $section) {
              $name = str_ireplace("$path/", '', $section);
              $name = str_ireplace('.blade.php', '', $name);
              $options[$name] = $name;
            }

            return $options;
          }),
      ])
      ->extraAttributes(['class' => 'w-full'])
      ->columns(1);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('title'),
        TextColumn::make('type'),
        TextColumn::make('slug'),
        TextColumn::make('component'),
        TextColumn::make('created_at'),
        TextColumn::make('updated_at'),
      ])
      ->groups([
        Group::make('page.title')
            ->label('Page #'),
      ])
      // ->striped()
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          // Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
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
      'index' => Pages\ListSections::route('/'),
      'create' => Pages\CreateSection::route('/create'),
      'edit' => Pages\EditSection::route('/{record}/edit'),
    ];
  }

  
  protected function updateSectionVariable(Model $record, array $data)
  {
    if (str_contains($data['value'], 'figure')) {
      preg_match_all('/<figure.*?<\/figure>/i', $data['value'], $figure);
      if (isset($figure[0])) {
        $figure = $figure[0];
        foreach ($figure as $item) {
          preg_match('/img\s+src="(.*?)"/i', $item, $img_src);
          $img_src = $img_src[1] ?? null;
          if ($img_src) {
            $img_path = preg_replace("/^.*?(\/storage.*?)$/is", "$1", $img_src);
            $img_url = url($img_path);
            $img = "<img src='$img_url' alt='Article image' />";
            $data['value'] = str_ireplace($item, $img, $data['value']);
          }
        }
      }
    }

    try {
      $record->update($data);
      Notification::make()
        ->title('Saved successfully')
        ->success()
        ->send();
    } catch (\Exception $e) {
      Notification::make()
        ->title($e->getMessage())
        ->error()
        ->send();
    }
  }
}
