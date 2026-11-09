<x-guest-layout>
    <!-- Panneau Gauche : Gradient & Visuel -->
    <div class="relative w-full lg:w-[46%] p-8 sm:p-12 text-white flex flex-col justify-between overflow-hidden min-h-[340px] lg:min-h-full rounded-2xl lg:rounded-[24px] m-2.5"
         style="background: radial-gradient(circle at 15% 15%, #93c5fd 0%, #6366f1 28%, #2563eb 55%, #1d4ed8 75%, #0f172a 100%);">
        
        <!-- Étoile / Badge en haut à gauche -->
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/25 flex items-center justify-center text-white text-2xl shadow-inner">
                <i class="bi bi-asterisk"></i>
            </div>
        </div>

        <!-- Décorations lumineuses abstraites -->
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-violet-400/30 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 left-10 w-64 h-64 rounded-full bg-blue-400/25 blur-2xl pointer-events-none"></div>

        <!-- Texte en bas -->
        <div class="relative z-10 pt-16 lg:pt-0">
            <span class="inline-block text-xs uppercase tracking-wider text-blue-100 font-semibold mb-3 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20">
                Plateforme d'interventions
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold leading-snug tracking-tight text-white mb-3">
                Accédez à votre espace pour piloter vos interventions
            </h2>
            <p class="text-blue-100/90 text-sm leading-relaxed max-w-sm">
                Centralisez vos techniciens, équipements et rapports en temps réel avec efficacité et clarté.
            </p>
        </div>
    </div>

    <!-- Panneau Droit : Formulaire -->
    <div class="w-full lg:w-[54%] p-8 sm:p-12 lg:p-14 flex flex-col justify-between bg-white">
        <div>
            <!-- Icône & En-tête -->
            <div class="flex items-center gap-2 mb-3">
                <span class="text-blue-600 text-2xl font-bold flex items-center">
                    <i class="bi bi-asterisk"></i>
                </span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Connexion</span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">
                Bienvenue à bord 👋
            </h1>
            <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                Renseignez vos identifiants pour accéder à votre tableau de bord et gérer vos interventions.
            </p>

            <!-- Statut de session éventuel -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Adresse e-mail
                    </label>
                    <div class="relative">
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="username"
                               placeholder="admin@exemple.com"
                               class="w-full px-4 py-3 rounded-xl border @error('email') border-rose-500 @else border-slate-200 @enderror bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 placeholder-slate-400 text-sm transition focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600" />
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <input id="password"
                               :type="showPassword ? 'text' : 'password'"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••••••"
                               class="w-full pl-4 pr-11 py-3 rounded-xl border @error('password') border-rose-500 @else border-slate-200 @enderror bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 placeholder-slate-400 text-sm transition focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10" />
                        
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition p-1"
                                tabindex="-1"
                                aria-label="Afficher ou masquer le mot de passe">
                            <i class="bi text-base" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600" />
                </div>

                <!-- Se souvenir & Mot de passe oublié -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900 select-none">
                        <input id="remember_me"
                               type="checkbox"
                               name="remember"
                               class="rounded-md border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 focus:ring-offset-0 w-4 h-4 cursor-pointer">
                        <span>Se souvenir de moi</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="font-semibold text-blue-600 hover:text-blue-700 transition">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <!-- Bouton de connexion style dégradé / pill bleu vibrant avec ombre -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3.5 px-6 rounded-xl font-semibold text-sm text-white shadow-lg shadow-blue-600/30 transition-all duration-200 transform active:scale-[0.99] hover:shadow-xl hover:shadow-blue-600/40 hover:-translate-y-0.5"
                            style="background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);">
                        Se connecter
                    </button>
                </div>
            </form>

            <!-- Séparateur décoratif & Accès rapide -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white text-slate-400 uppercase tracking-wider font-medium">Espace sécurisé</span>
                </div>
            </div>

            <!-- Badges d'accès d'assistance -->
            <!-- <div class="grid grid-cols-3 gap-2.5 text-center">
                <div class="py-2.5 px-2 bg-slate-50 hover:bg-slate-100/80 rounded-xl text-slate-600 transition flex items-center justify-center gap-1.5 text-xs font-medium border border-slate-100">
                    <i class="bi bi-shield-check text-blue-600"></i>
                    <span>SSL 256-bit</span>
                </div>
                <div class="py-2.5 px-2 bg-slate-50 hover:bg-slate-100/80 rounded-xl text-slate-600 transition flex items-center justify-center gap-1.5 text-xs font-medium border border-slate-100">
                    <i class="bi bi-lightning-charge text-amber-500"></i>
                    <span>Haute dispo</span>
                </div>
                <div class="py-2.5 px-2 bg-slate-50 hover:bg-slate-100/80 rounded-xl text-slate-600 transition flex items-center justify-center gap-1.5 text-xs font-medium border border-slate-100">
                    <i class="bi bi-headset text-indigo-500"></i>
                    <span>Support</span>
                </div>
            </div>
        </div> -->

        <!-- Pied de page -->
        <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            @if (Route::has('register'))
                Vous n'avez pas de compte ?
                <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 ml-1">
                    Créer un compte
                </a>
            @else
                <span class="text-slate-400">© {{ date('Y') }} Gestion des Interventions. Tous droits réservés.</span>
            @endif
        </div>
    </div>
</x-guest-layout>
