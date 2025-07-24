# Laravel PolyLang

Flexible polymorphic translation system for Laravel 11+ — clean, simple, and model-agnostic.

---

## 🔧 Installation

Require the package via Composer:

```bash
composer require mohamed-ahmed/laravel-polylang
```

---

## ⚙️ Publish & Migrate

Publish the config and migration files:

```bash
php artisan vendor:publish --tag=config
php artisan vendor:publish --tag=migrations
php artisan migrate
```

---

## 🧩 Usage

### ✅ 1. Add the `Translatable` Trait to Your Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MohamedAhmed\LaravelPolyLang\Traits\Translatable;

class Item extends Model
{
    use Translatable;

    protected $fillable = [
        'slug',
        'price',
    ];

    // Translatable fields: name, description
}
```

---

### ✅ 2. Create Controller with Store and Show Methods

```php
namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // 🌐 Create a new item with translations
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|unique:items',
            'price' => 'required|numeric',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string',
            'translations.*.description' => 'required|string',
        ]);

        $item = Item::create([
            'slug' => $validated['slug'],
            'price' => $validated['price'],
        ]);

        foreach ($request->input('translations') as $locale => $fields) {
            foreach ($fields as $field => $value) {
                $item->setTranslation($field, $value, $locale);
            }
        }

        return response()->json(['success' => true, 'id' => $item->id], 201);
    }

    // 🌐 Retrieve item in the requested language
    public function show(Request $request, $id)
    {
        $item = Item::with('translations')->findOrFail($id);
        $locale = app()->getLocale();

        return response()->json([
            'id' => $item->id,
            'slug' => $item->slug,
            'price' => $item->price,
            'translations' => $item->getTranslatedAttributes(['name', 'description'], $locale),
        ]);
    }
}
```

---

### ✅ 3. Define API Routes (`routes/api.php`)

```php
use App\Http\Controllers\ItemController;
use MohamedAhmed\LaravelPolyLang\Middleware\SetLocale;

Route::middleware([SetLocale::class])->group(function () {
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
});
```

---

### ✅ 4. Make a Request

#### ✅ Create an Item

**POST** `/api/items`

```json
{
  "slug": "laptop-pro",
  "price": 1899.99,
  "translations": {
    "en": {
      "name": "Laptop Pro",
      "description": "High-end performance laptop."
    },
    "ar": {
      "name": "حاسوب برو",
      "description": "حاسوب محمول عالي الأداء."
    }
  }
}
```

#### ✅ Fetch an Item with Language Preference

**GET** `/api/items/1`

Add the `Accept-Language` header:

```
Accept-Language: ar
```

**Response:**

```json
{
  "id": 1,
  "slug": "laptop-pro",
  "price": 1899.99,
  "translations": {
    "name": "حاسوب برو",
    "description": "حاسوب محمول عالي الأداء."
  }
}
```

If Arabic is missing, it automatically falls back to `config('app.locale')`.

---

## 🧠 How It Works

- Translations are stored in a single polymorphic table.
- You can add translations for any model without modifying the schema.
- Automatically uses the language from `Accept-Language` header via `SetLocale` middleware.
- Falls back to `config('app.locale')` if the requested translation is missing.

---

## 📂 Example Migration Output

Creates a `translations` table like:

```bash
id | translatable_type | translatable_id | locale | field       | value
---|-------------------|-----------------|--------|-------------|---------------------------
1  | App\Models\Item   | 1               | en     | name        | Laptop Pro
2  | App\Models\Item   | 1               | ar     | name        | حاسوب برو
3  | App\Models\Item   | 1               | en     | description | High-end performance laptop.
```

---

## 📜 License

MIT License © [Mohamed Ahmed](mailto:mohamedgcoder@gmail.com)

---

## 🙌 Contributions

Issues and PRs are welcome! 🎉
