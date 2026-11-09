Bonjour {!! $intervention->technicien->nom !!},

Une nouvelle intervention vous a été attribuée.

Client : {!! $intervention->client->nom !!}
Problème : {!! $intervention->titre !!}
Date : {{ $intervention->date_signalement->format('d/m/Y') }}

Veuillez consulter votre espace de gestion des interventions pour plus de détails.

Cordialement,
L'équipe de gestion des interventions