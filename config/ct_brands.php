<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reglas Generales de Filtrado
    |--------------------------------------------------------------------------
    | "Si la marca tiene CERO Stock o no más de 3 productos, NO"
    */
    'rules' => [
        'min_products'  => 4,    // Debe tener al menos 4 productos (más de 3)
        'require_stock' => true, // Debe tener stock mayor a 0 en la suma total
    ],

    /*
    |--------------------------------------------------------------------------
    | Marcas Aprobadas (SI)
    |--------------------------------------------------------------------------
    | Se importará el catálogo de estas marcas (siempre y cuando cumplan
    | con las reglas generales de stock y volumen).
    */
    'approved' => [
        'Acteck', 'Amazfit', 'Amazon', 'Amd', 'Aoc', 'Aruba', 'Aspel', 
        'Autodesk', 'Azor', 'Baco', 'Badgy', 'Be Quiet', 'Benq', 'Bic', 
        'Brobotix', 'Brother', 'Cisco', 'Citizen', 'Clar Systems', 'Contpaqi',
        'Contpaqi Contabilidad', 'Corsair', 'Creative Labs', 'Ct Cloud', 'Datamax', 
        'Dess', 'Ec-line', 'Ecs', 'Epson', 'Evolis', 'Gigabyte', 'Go Safe', 'Google', 
        'Hawlett Packard Enterprise','Hitachi', 'Honeywell', 'Impress', 'Intel', 'Intermec', 
        'Janel', 'Kensington', 'Kingston Technology', 'Koblenz','Kores', 'Ksa', 'Laces', 
        'Lacie', 'Lexmark', 'Lg', 'Logitech', 'Manhattan', 'Microsoft','Nextep', 'Okidata', 
        'Ovaltech', 'Pacific Soft', 'Panasonic', 'Pcm', 'Pny', 'Poly', 'Prolicom', 'Qian', 
        'Sabo', 'Sandisk', 'Seagate', 'Silimex', 'Star Micronics', 'Startech.com', 'Synology', 
        'Toshiba', 'Vankyo', 'Verbatim', 'Western Digital', 'Wilson Jones',

        //?SYSCOM
        'Allied Telesis', 'Alter', 'Anviz', 'Apc', 'Belden', 'Bolide', 
        'Cambium Networks', 'Cdp', 'Complet', 'Condumex', 'Condunet', 
        'Cyberpower', 'Datalogic', 'Datashield', 'Dten', 'Enson', 'Epcom', 'Ezviz', 'Grandstream',
        'Hikvision', 'Hillstone', 'Hilook', 'Imou', 'Indiana', 'Intellinet', 'Jc Vision', 'Linkedpro', 
        'Linksys',
        'Mikrotik', 'Multimedia Screens', 'Netis', 
        'Nexxt Solutions Home', 'North System', 'Omada', 'Orvibo', 
        'Panduit', 'Planet', 'Provision-isr', 'Ruijie', 'Saxxon', 
        'Ske', 'Smartbitt', 'Syble', 'Teltonika', 'Tenda', 'Topaz', 'Tripp-lite', 'Ubiquiti',
        'Uniarch', 'Unitech', 'Uniview', 'Vica', 'Vision', 'Wam', 'Xfusion', 'Youjie', 'Zebra', 'Zk Teco'
    ],

    /*
    |--------------------------------------------------------------------------
    | Marcas Rechazadas (NO)
    |--------------------------------------------------------------------------
    | NO se importará absolutamente nada de estas marcas bajo ninguna circunstancia.
    */
    'rejected' => [
        'Acer', 'Adata', 'Avast', 'Balam Rush', 'Barkan', 'Barrilito', 
        'Bin Zun', 'Biostar', 'Bitdefender', 'Bixolon', 'Blackberry', 
        'Canon', 'Cooler Master', 'Cougar', 'Dahua Technology', 'Dbugg', 
        'Deepcool', 'Diem', 'Easy Line', 'Easy Smart', 'Ecobond', 'Elotouch',
        'Eset', 'Eurocolors', 'Euromac', 'Evorok', 'Evotec', 'Game Factor', 
        'Generico', 'Geo', 'Getttech', 'Getttech Gaming', 'Hi8us', 'Hid', 'Highlink', 
        'Hisense', 'Hostech', 'Huawei', 'Hune', 'Hyundai', 'Immortal','Infocus', 'Ingressio', 
        'Jabra', 'Kangji', 'Kaspersky', 'Kodak', 'Kxd', 'Kyocera', 'Lanix', 'Lefort', 
        'Lf Acoustics', 'Mae', 'Mapasa', 'Megapower', 'Mercusys','Mimosa Networks', 'Mobifree', 
        'Msi', 'Mybusiness', 'Naceb Gaming', 'Naceb Technology', 'Nassa', 
        'Necnon', 'New Pull', 'Newland', 'Nintendo', 'Norton', 'Offiz', 
        'Oppo', 'Pantum', 'Paperline', 'Pegaso', 'Perfect Choice', 
        'Plantronics', 'Polaroid', 'Polycom', 'Quinyx', 'Redragon', 'Roku', 
        'Smx', 'Soft Restaurant', 'Sonnoc', 'Sonolife', 'Sony', 'Steelseries', 
        'Stylos', 'Swann', 'Targus', 'Tcl', 'Techzone', 'Thermaltake', 'Tp-link', 'Uni Paint',
        'Up', 'Urban Balance', 'Urovo', 'Valve', 'Vangogh', 'Vorago', 'Vortred',
        'Xbox', 'Xiaomi', 'Xpg', 'Xzeal', 'Yaber', 'Yeyian', 'Yobekan'
    ],

    /*
    |--------------------------------------------------------------------------
    | Marcas Condicionales (EXP)
    |--------------------------------------------------------------------------
    | Solo importar ciertos SKUs o Categorías de estas marcas.
    */
    'conditional' => [
        'Apple' => [
            'allowed_categories' => ['Computo', 'Tablets', 'Accesorios'], // Ejemplo
            'allowed_skus' => [], 
        ],
        'Asus' => [
            'allowed_categories' => [],
            'allowed_skus' => [],
        ],
        'Asus Business' => [
            'allowed_categories' => [],
            'allowed_skus' => [],
        ],
        'Dell' => [
            'allowed_categories' => [],
            'allowed_skus' => [],
        ],
        'Hp' => [
            'allowed_categories' => [],
            'allowed_skus' => [],
        ],
        'Lenovo' => [
            'allowed_categories' => [],
            'allowed_skus' => [],
        ],
        'Samsung' => [
            'allowed_categories' => ['Monitores'], // Ejemplo
            'allowed_skus' => [],
        ],
        'Xerox' => [
            'allowed_categories' => [],
            'allowed_skus' => [],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Marcas en Evaluación / Pendientes (?)
    |--------------------------------------------------------------------------
    | Estas se ignoran por ahora. En el futuro, la API secundaria (Syscom)
    | decidirá si entran o no.
    */
    'ignored' => [

    ]
];