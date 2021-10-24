<x-app-layout>
    <div class="container lg:w-3/4 md:w-4/5 w-11/12 mx-auto my-8 px-4 py-4 bg-white shadow-md">

        <x-flash-message :message="session('notice')" />
        <x-validation-errors :errors="$errors" />

        <h2 class="flex justify-center font-bold text-lg my-4">メッセージ</h2>
        <div class="">
            @foreach ($messages as $message)
                @if ($message->send_by == $send_by)
                    <div class="flex flex-col sm:flex-row items-center text-center my-4">
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $sender->profile_photo_url }}"
                            alt="{{ $sender->name }}" />
                    </div>
                    <p class="text-gray-700 text-base text-left">{{ $message->message }}</p>
                @else
                    <div class="flex flex-col sm:flex-row items-center sm:justify-end text-center my-4">
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $partner->profile_photo_url }}"
                            alt="{{ $partner->name }}" />
                    </div>
                    <p class="text-gray-700 text-base text-right">{{ $message->message }}</p>
                @endif
            @endforeach
            <form action="{{ route('job_offers.users.messages.store', [$jobOffer, $user]) }}" method="POST"
                class="rounded pt-3 pb-8 mb-4">
                @csrf
                <input type="text" name="message"
                    class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-pink-600 w-full py-2 px-3"
                    required placeholder="Message" value="{{ old('message') }}">
                <input type="submit" value="Send"
                    class="w-full flex justify-center bg-gradient-to-r from-indigo-500 to-blue-600 hover:bg-gradient-to-l hover:from-blue-500 hover:to-indigo-600 text-gray-100 p-2 rounded-full tracking-wide font-semibold shadow-lg cursor-pointer transition ease-in duration-500">
                <input type="hidden" value="{{ $sender->id }}" name="sender_id">
                <input type="hidden" value="{{ $partner->id }}" name="partner_id">
            </form>
        </div>
    </div>
</x-app-layout>
