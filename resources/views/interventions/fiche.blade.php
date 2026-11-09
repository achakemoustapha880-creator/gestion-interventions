<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fiche d'intervention {{ 'INT-' . str_pad($intervention->id, 5, '0', STR_PAD_LEFT) }}</title>

    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; font-size: 12px; margin: 0; background: #e5e7eb; }

        .barre { max-width: 794px; margin: 16px auto 0; display: flex; gap: 8px; }
        .barre a, .barre button {
            font: inherit; cursor: pointer; text-decoration: none;
            padding: 8px 16px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #1e293b;
        }
        .barre button { background: #2563eb; border-color: #2563eb; color: #fff; font-weight: bold; }

        .page { width: 794px; max-width: 100%; margin: 12px auto 24px; background: #fff; padding: 36px 44px; }

        .entete { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #0b2447; padding-bottom: 10px; }
        .entete h1 { margin: 0; font-size: 20px; color: #0b2447; }
        .entete .sous { color: #64748b; margin-top: 3px; }
        .ref { text-align: right; }
        .ref .num { font-size: 17px; font-weight: bold; color: #0b2447; }

        h2 {
            font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: #fff;
            background: #0b2447; padding: 5px 10px; margin: 14px 0 0;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }

        table.infos { width: 100%; border-collapse: collapse; }
        table.infos td { border: 1px solid #cbd5e1; padding: 5px 10px; vertical-align: top; }
        table.infos td.lib {
            width: 28%; background: #f1f5f9; font-weight: bold; color: #475569;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }

        .zone { border: 1px solid #cbd5e1; border-top: 0; padding: 8px 10px; min-height: 50px; white-space: pre-line; }
        .zone.grande { min-height: 100px; }

        .signatures { display: flex; gap: 20px; margin-top: 16px; }
        .signature { flex: 1; border: 1px solid #cbd5e1; height: 85px; padding: 6px 8px; }
        .signature .titre { font-weight: bold; color: #475569; font-size: 11px; }
        .signature .nom { color: #64748b; font-size: 11px; margin-top: 2px; }

        .pied { margin-top: 12px; text-align: center; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 6px; }

        /* ---------- Impression : une seule page A4, sans en-tête ni pied du navigateur ---------- */
        @page { size: A4; margin: 0; }

        @media print {
            html, body { background: #fff; width: 210mm; height: 297mm; margin: 0; padding: 0; }
            .barre { display: none; }
            .page {
                width: 210mm; max-width: none; height: 296mm; max-height: 296mm; overflow: hidden;
                margin: 0; padding: 12mm 14mm;
                page-break-after: avoid; page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="barre">
    <button onclick="window.print()">🖨 Imprimer / Enregistrer en PDF</button>
    <a href="{{ route('interventions.show', $intervention) }}">← Retour à l'intervention</a>
</div>

<div class="page">
    <div class="entete">
        <div>
            <h1>FICHE D'INTERVENTION</h1>
            <div class="sous">Intervention informatique</div>
        </div>
        <div class="ref">
            <div class="num">{{ 'INT-' . str_pad($intervention->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div>Éditée le {{ now()->format('d/m/Y') }}</div>
            <div><strong>{{ $intervention->statut->label() }}</strong></div>
        </div>
    </div>

    <h2>Client</h2>
    <table class="infos">
        <tr><td class="lib">Nom</td><td>{{ $intervention->client->nom }}</td></tr>
        <tr><td class="lib">Téléphone</td><td>{{ $intervention->client->telephone }}</td></tr>
        <tr><td class="lib">Adresse</td><td>{{ $intervention->client->adresse ?? '—' }}</td></tr>
    </table>

    <h2>Équipements nécessaires</h2>
    <table class="infos">
        @forelse ($intervention->equipements as $e)
            <tr>
                <td>{{ $e->nom }}{{ $e->type ? ' ('.$e->type.')' : '' }}</td>
                <td style="width:30%">{{ $e->reference ? 'Réf. '.$e->reference : '' }}</td>
                <td style="width:60px; text-align:center">x{{ $e->pivot->quantite }}</td>
            </tr>
        @empty
            <tr><td>Aucun équipement indiqué</td></tr>
        @endforelse
    </table>

    <h2>Intervention</h2>
    <table class="infos">
        <tr><td class="lib">Problème signalé</td><td>{{ $intervention->titre }}</td></tr>
        <tr><td class="lib">Type</td><td>{{ $intervention->type_intervention->label() }}</td></tr>
        <tr><td class="lib">Priorité</td><td>{{ $intervention->priorite->label() }}</td></tr>
        <tr><td class="lib">Technicien</td><td>{{ $intervention->technicien?->nom ?? 'Non assigné' }}</td></tr>
        <tr><td class="lib">Date de signalement</td><td>{{ $intervention->date_signalement->format('d/m/Y') }}</td></tr>
        <tr><td class="lib">Début</td><td>{{ $intervention->date_debut?->format('d/m/Y H:i') ?? '—' }}</td></tr>
        <tr><td class="lib">Fin</td><td>{{ $intervention->date_fin?->format('d/m/Y H:i') ?? '—' }}</td></tr>
    </table>

    <h2>Description du problème</h2>
    <div class="zone">{{ $intervention->description ?? '—' }}</div>

    <h2>Travaux réalisés / observations</h2>
    <div class="zone grande"></div>

    <div class="signatures">
        <div class="signature">
            <div class="titre">Signature du technicien</div>
            <div class="nom">{{ $intervention->technicien?->nom }}</div>
        </div>
        <div class="signature">
            <div class="titre">Signature du client</div>
            <div class="nom">{{ $intervention->client->nom }}</div>
        </div>
    </div>

    <div class="pied">Document généré par l'application de gestion des interventions</div>
</div>

</body>
</html>