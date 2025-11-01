<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('プロフィール情報') }}
        </h2>

        <p class="mt-1 text-base text-gray-600">
            {{ __('アカウントのプロフィール情報とメールアドレスを更新します。') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('氏名')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('メールアドレス')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-base mt-2 text-gray-800">
                        {{ __('メールアドレスが認証されていません。') }}

                        <button form="send-verification" class="underline text-base text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('認証メールを再送信する') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-base text-green-600">
                            {{ __('認証リンクをメールアドレスに送信しました。') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- プロフィール画像 -->
        <div>
            <x-input-label for="profile_image" :value="__('プロフィール画像')" />
            @if($user->profile_image_path)
                <div class="mt-2 mb-2">
                    <img src="{{ asset('storage/' . $user->profile_image_path) }}" 
                         alt="プロフィール画像" 
                         class="w-24 h-24 rounded-full object-cover border border-gray-300">
                </div>
            @endif
            <input type="file" 
                   id="profile_image" 
                   name="profile_image" 
                   accept="image/*"
                   class="mt-1 block w-full text-base text-gray-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-lg file:border-0
                          file:text-base file:font-semibold
                          file:bg-indigo-50 file:text-indigo-700
                          hover:file:bg-indigo-100">
            <p class="mt-1 text-xs text-gray-500">対応形式: JPG, PNG, GIF（最大5MB）</p>
            <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />
        </div>

        <!-- 自己紹介 -->
        <div>
            <x-input-label for="self_introduction" :value="__('自己紹介・備考')" />
            <textarea id="self_introduction" 
                      name="self_introduction" 
                      rows="5"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('self_introduction', $user->self_introduction) }}</textarea>
            <p class="mt-1 text-xs text-gray-500">1000文字以内で自己紹介や備考を入力できます。</p>
            <x-input-error class="mt-2" :messages="$errors->get('self_introduction')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('保存') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-base text-gray-600"
                >{{ __('保存しました。') }}</p>
            @endif
        </div>
    </form>
</section>
