<?php

// app/Http/Controllers/ReaderController.php

namespace App\Http\Controllers;

use App\Models\Reader;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReaderController extends Controller
{
    public function profile()
    {
        $reader = Auth::user()->reader;
        $activeLoans = $reader->activeLoans()->with('book')->get();
        $loanHistory = $reader->loans()
            ->with('book')
            ->whereIn('status', ['returned', 'lost', 'damaged'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('reader.profile', compact('reader', 'activeLoans', 'loanHistory'));
    }

    public function searchBooks(Request $request)
    {
        $request->validate([
            'query' => 'required|min:3'
        ]);

        // Здесь будет поиск книг через API
        return response()->json([]);
    }

    public function requestLoan(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $reader = Auth::user()->reader;

        if (!$reader->canBorrow()) {
            return back()->with('error', 'Вы не можете взять книгу. Проверьте статус вашего читательского билета.');
        }

        // Создание запроса на выдачу
        // В реальном приложении здесь будет создание заявки на одобрение библиотекарем

        return back()->with('success', 'Запрос на выдачу книги отправлен. Ожидайте подтверждения библиотекаря.');
    }
}
