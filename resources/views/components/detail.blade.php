<div class="p-2">
    <table class="table-auto border-collapse w-full text-center bg-gray-200 rounded-md overflow-hidden shadow-sm">
        <tbody>
            @if (!empty($row->statusCredito()))
                <tr>
                    <td class="border px-2 py-2">
                        Estado Crediticio :
                        {{ $row->statusCredito()['reestructurados'] ?? '#' }}
                    </td>
                    <td class="border px-2 py-2">
                        Estado Social :
                        {{ $row->statusSocial($row->statusCredito()['unid_hab_id'])['estado_social_benef_final'] ?? '#' }}
                    </td>
                    <td class="border px-2 py-2">
                        Estado Legal :
                        {{ $row->statusLegal($row->statusCredito()['unid_hab_id'])['observaciones1'] ?? '#' }}
                    </td>
                </tr>
            @else
                <tr>
                    <td class="border px-2 py-2">
                        Sin informacion disponible en las areas paralelas.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
