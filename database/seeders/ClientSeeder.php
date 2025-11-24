<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'nombre' => 'María Elena Sánchez',
                'nit' => '12345678-9',
                'ci' => '87654321',
                'telf' => '70123456',
            ],
            [
                'nombre' => 'Carlos Alberto López',
                'nit' => '98765432-1',
                'ci' => '12345678',
                'telf' => '71234567',
            ],
            [
                'nombre' => 'Ana Patricia Morales',
                'nit' => null,
                'ci' => '23456789',
                'telf' => '72345678',
            ],
            [
                'nombre' => 'José Miguel Rivera',
                'nit' => '11223344-5',
                'ci' => '34567890',
                'telf' => '73456789',
            ],
            [
                'nombre' => 'Fernanda Alejandra Cruz',
                'nit' => null,
                'ci' => '45678901',
                'telf' => '74567890',
            ],
            [
                'nombre' => 'Roberto Daniel Vargas',
                'nit' => '22334455-6',
                'ci' => '56789012',
                'telf' => '75678901',
            ],
            [
                'nombre' => 'Carmen Rosa Gutiérrez',
                'nit' => '33445566-7',
                'ci' => '67890123',
                'telf' => '76789012',
            ],
            [
                'nombre' => 'Luis Fernando Castillo',
                'nit' => null,
                'ci' => '78901234',
                'telf' => '77890123',
            ],
            [
                'nombre' => 'Gabriela Isabel Mendoza',
                'nit' => '44556677-8',
                'ci' => '89012345',
                'telf' => '78901234',
            ],
            [
                'nombre' => 'Francisco Javier Herrera',
                'nit' => '55667788-9',
                'ci' => '90123456',
                'telf' => '79012345',
            ],
            [
                'nombre' => 'Verónica Beatriz Salazar',
                'nit' => null,
                'ci' => '01234567',
                'telf' => '70234561',
            ],
            [
                'nombre' => 'Eduardo Antonio Romero',
                'nit' => '66778899-0',
                'ci' => '13579246',
                'telf' => '71345672',
            ],
            [
                'nombre' => 'Mónica Alejandra Flores',
                'nit' => '77889900-1',
                'ci' => '24681357',
                'telf' => '72456783',
            ],
            [
                'nombre' => 'Héctor Daniel Quispe',
                'nit' => null,
                'ci' => '35792468',
                'telf' => '73567894',
            ],
            [
                'nombre' => 'Silvia Marina Torres',
                'nit' => '88990011-2',
                'ci' => '46803579',
                'telf' => '74678905',
            ],
            [
                'nombre' => 'Construcciones del Valle S.R.L.',
                'nit' => '1023456789-3',
                'ci' => null,
                'telf' => '22334455',
            ],
            [
                'nombre' => 'Ferretería San José',
                'nit' => '2034567890-4',
                'ci' => null,
                'telf' => '22445566',
            ],
            [
                'nombre' => 'Inversiones Técnicas Ltda.',
                'nit' => '3045678901-5',
                'ci' => null,
                'telf' => '22556677',
            ],
            [
                'nombre' => 'Materiales de Construcción ABC',
                'nit' => '4056789012-6',
                'ci' => null,
                'telf' => '22667788',
            ],
            [
                'nombre' => 'Distribuidora Industrial EIRL',
                'nit' => '5067890123-7',
                'ci' => null,
                'telf' => '22778899',
            ]
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }
}