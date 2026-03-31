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
        //Filtro por SKU
        'excluded_skus' => [
            'BM4G5AT#ABM','BT3E5AT#ABM','BP0L4AT#ABM','B0CP3LA','JMF3D','0F6GX','0Y8RM','EP20AN2 N100 4G/128G W11P',
            '90NX08X1-M004B0','90NX08W1-M003R0','90NX03E1-M00WM0','CZ1104CM2A-NS0084','90NX0781-M009C0','90NX06J2-M002E0',
            '90NX07P2-M00MP0','90NR0MZ6-M001H0','90NR0BV7-M00NT0','90NR0N06-M00570','90NR0N06-M00580','90NR0KQ1-M00130',
            '90NR0KV1-M005X0','90NR0NB1-M00490','0NR0KW8-M002Z0','90NR0JY1-M000U0','90NR0LT1-M000R0','90NR0LD1-M001R0',
            '90NR0LG1-M00BM0','90NR0LD1-M007V0','9L8Y7LA#ABM'
        ],
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

        //
        'Apple', 'Asus', 'Asus Business', 'Dell', 'Hp', 'Lenovo', 'Samsung', 'Xerox',

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
];