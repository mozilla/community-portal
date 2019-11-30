<?php

    $visibility_options = Array(
        PrivacySettings::REGISTERED_USERS   =>  __('Registered Users'),
        PrivacySettings::PUBLIC_USERS   =>  __('Public (Everyone)'),
        PrivacySettings::PRIVATE_USERS   =>  __('Private (Only Me)'),
    );

    $template_dir = get_template_directory();

    $pronouns = Array(
        'She/Her',
        'He/Him',
        'They/Them'
    );

    $languages = Array(
        'ab' => 'Abkhazian',
        'abr'=> 'Abron',
        'ace'=> 'Achinese',
        'ach'=> 'Acoli',
        'ada'=> 'Adangme',
        'ady'=> 'Adyghe',
        'aa' => 'Afar',
        'af' => 'Afrikaans',
        'agq'=> 'Aghem',
        'ak' => 'Akan',
        'bss'=> 'Akoose',
        'sq' => 'Albanian',
        'bhk'=> 'Albay Bicolano',
        'arq'=> 'Algerian Arabic',
        'am' => 'Amharic',
        'amo'=> 'Amo',
        'njo'=> 'Ao Naga',
        'ar' => 'Arabic',
        'an' => 'Aragonese',
        'hy' => 'Armenian',
        'frp'=> 'Arpitan',
        'as' => 'Assamese',
        'ast'=> 'Asturian',
        'asa'=> 'Asu',
        'atj'=> 'Atikamekw',
        'cch'=> 'Atsam',
        'av' => 'Avaric',
        'awa'=> 'Awadhi',
        'ay' => 'Aymara',
        'az' => 'Azerbaijani',
        'az_Arab'   =>  'Azerbaijani (Arabic)',
        'az_Cyrl'   =>  'Azerbaijani (Cyrillic)',
        'bfq'=> 'Badaga',
        'ksf'=> 'Bafia',
        'bfd'=> 'Bafut',
        'bfy'=> 'Bagheli',
        'bqi'=> 'Bakhtiari',
        'bjt'=> 'Balanta-Ganja',    
        'ban'=> 'Balinese',
        'bgx'=> 'Balkan Gagauz Turkish',
        'bft'=> 'Balti',
        'bal'=> 'Baluchi',
        'bm' => 'Bambara',
        'bm_Nkoo'   =>  'Bambara (N’Ko)',
        'bax'=> 'Bamun',
        'bn' => 'Bangla',
        'bjn'=> 'Banjar',
        'bap'=> 'Bantawa',
        'bci'=> 'Baoulé',
        'bas'=> 'Basaa',
        'ba' => 'Bashkir',
        'eu' => 'Basque',
        'bsc'=> 'Bassari',
        'bbc'=> 'Batak Toba',
        'btv'=> 'Bateri',
        'bar'=> 'Bavarian',
        'bej'=> 'Beja',
        'be' => 'Belarusian',
        'bem'=> 'Bemba',
        'bez'=> 'Bena',
        'bew'=> 'Betawi',
        'bhi'=> 'Bhilali',
        'bhb'=> 'Bhili',
        'bho'=> 'Bhojpuri',
        'bik'=> 'Bikol',
        'bin'=> 'Bini',
        'bpy'=> 'Bishnupriya',
        'bi' => 'Bislama',
        'byn'=> 'Blin',
        'brx'=> 'Bodo',
        'bmq'=> 'Bomu',
        'bs' => 'Bosnian',
        'bs_Cyrl' => 'Bosnian (Cyrillic)',
        'brh'=> 'Brahui',
        'bra'=> 'Braj',
        'br' => 'Breton',
        'bvb'=> 'Bube',
        'bug'=> 'Buginese',
        'bku'=> 'Buhid',
        'bg' => 'Bulgarian',
        'bum'=> 'Bulu',
        'bua'=> 'Buriat',
        'my' => 'Burmese',
        'buc'=> 'Bushi',
        'frc'=> 'Cajun French',
        'yue'=> 'Cantonese',
        'yue_Hans'  =>  'Cantonese (Simplified)',
        'cps'=> 'Capiznon',
        'ca' => 'Catalan',
        'sef'=> 'Cebaara Senoufo',
        'ceb'=> 'Cebuano',
        'tzm'=> 'Central Atlas Tamazight',
        'dtp'=> 'Central Dusun',
        'nch'=> 'Central Huasteca Nahuatl',
        'ckb'=> 'Central Kurdish',
        'maz'=> 'Central Mazahua',
        'ryu'=> 'Central Okinawan',
        'esu'=> 'Central Yupik',
        'fuq'=> 'Central-Eastern Niger Fulfulde',
        'ccp'=> 'Chakma',
        'ce' => 'Chechen',
        'chr'=> 'Cherokee',
        'hne'=> 'Chhattisgarhi',
        'cic'=> 'Chickasaw',
        'qug'=> 'Chimborazo Highland Quichua',
        'zh' => 'Chinese',
        'chp'=> 'Chipewyan',
        'cho'=> 'Choctaw',
        'cu' => 'Church Slavonic',
        'chk'=> 'Chuukese',
        'cv' => 'Chuvash',
        'ksh'=> 'Colognian',
        'swb'=> 'Comorian',
        'kw' => 'Cornish',
        'co' => 'Corsican',
        'cr' => 'Cree',
        'crh'=> 'Crimean Turkish',
        'hr' => 'Croatian',
        'cs' => 'Czech',
        'dak'=> 'Dakota',
        'dnj'=> 'Dan',
        'thl'=> 'Dangaura Tharu',
        'da' => 'Danish',
        'dar'=> 'Dargwa',
        'dcc'=> 'Deccan',
        'dv' => 'Divehi',
        'doi'=> 'Dogri',
        'rmt'=> 'Domari',
        'dty'=> 'Dotyali',
        'dua'=> 'Duala',
        'nl' => 'Dutch',
        'dyu'=> 'Duala',
        'dz' => 'Dzongkha',
        'fud'=> 'East Futuna',
        'cjm'=> 'Eastern Cham',
        'frs'=> 'Eastern Frisian',
        'nhe'=> 'Eastern Huasteca Nahuatl',
        'lwl'=> 'Eastern Lawa',
        'mgp'=> 'Eastern Magar',
        'taj'=> 'Eastern Tamang',
        'efi'=> 'Efik',
        'arz'=> 'Egyptian Arabic',
        'ebu'=> 'Embu',
        'egl'=> 'Emilian',
        'en' => 'English',
        'myv'=> 'Erzya',
        'eo' => 'Esperanto',
        'et' => 'Estonian',
        'ee' => 'Ewe',
        'ewo'=> 'Ewondo',
        'ext'=> 'Extremaduran',
        'fan'=> 'Fang',
        'fo' => 'Faroese',
        'hif'=> 'Fiji Jindi',
        'fj' => 'Fijian',
        'fil'=> 'Filipino',
        'fi' => 'Finnish',
        'fon'=> 'Fon',
        'gur'=> 'Frafra',
        'fr' => 'French',
        'fur'=> 'Friulian',
        'ff' => 'Fulah',
        'ff_Adlm'   => 'Fulah (Adlam)',
        'fvr'=> 'Fur',
        'gaa'=> 'Ga',
        'gag'=> 'Gaguaz',
        'gl' => 'Galician',
        'gan'=> 'Gan Chinese',
        'lg' => 'Ganda',
        'gbm'=> 'Garhwali',
        'grt'=> 'Garo',
        'gay'=> 'Gayo',
        'ka' => 'Georgian',
        'de' => 'German',
        'aln'=> 'Gheg Albanian',
        'bbj'=> 'Ghomala',
        'glk'=> 'Gilaki',
        'gil'=> 'Gilbertese',
        'gom'=> 'Goan Konkani',
        'gon'=> 'Gondi',
        'gor'=> 'Gorontalo',
        'el' => 'Greek',
        'gos'=> 'Gronings',
        'gn' => 'Guarani',
        'gub'=> 'Guajajára',
        'gu' => 'Gujarati',
        'gju'=> 'Gujari',
        'gvr'=> 'Gurung',
        'guz'=> 'Gusii',
        'gwi'=> 'Gwichʼin',
        'hoj'=> 'Hdothi',
        'ht' => 'Haitian Creole	',
        'hak'=> 'Hakka Chinese',
        'hnn'=> 'Hanunoo',
        'bgc'=> 'Haryanvi',
        'mey'=> 'Hassaniyya',
        'ha' => 'Hausa',
        'ha_Arab'=> 'Hausa (Arabic)',
        'haw'=> 'Hawaiian',
        'haz'=> 'Hazaragi',
        'he' => 'Hebrew',
        'hz' => 'Herero',
        'hil'=> 'Hiligaynon',
        'hi' => 'Hindi',
        'ho' => 'Hiri Motu',
        'hoc'=> 'Ho',
        'hu' => 'Hungarian',
        'iba'=> 'Iban',
        'ibb'=> 'Ibibio',
        'is' => 'Icelandic',
        'ife'=> 'Ifè',
        'ig' => 'Igbo',
        'ilo'=> 'Iloko',
        'smn'=> 'Inari Sami',
        'id' => 'Indonesian',
        'mvy'=> 'Indus Kohistani',
        'izh'=> 'Ingrian',
        'inh'=> 'Ingush',
        'ia' => 'Interlingua',
        'ikt'=> 'Inuinnaqtun',
        'iu' => 'Inuktitut',
        'iu_Latn' => 'Inuktitut (Latin)',
        'ik' => 'Inupiaq',
        'ga' => 'Irish',
        'it' => 'Italian',
        'jam'=> 'Jamaican Creole English',
        'ja' => 'Japanese',
        'jv' => 'Javanese',
        'bze'=> 'Jenaama Bozo',
        'kaj'=> 'Jju',
        'dyo'=> 'Jola-Fonyi',
        'jml'=> 'Jumli',
        'jut'=> 'Jutish', 
        'kbd'=> 'Kabardian',
        'kea'=> 'Kabuverdianu',
        'kab'=> 'Kabyle',
        'kfr'=> 'Kachhi',
        'gjk'=> 'Kaingang',
        'kkj'=> 'Kako',
        'kl' => 'Kalaallisut',
        'kck'=> 'Kalanga',
        'kln'=> 'Kalenjin',
        'rmf'=> 'Kalo Finnish Romani',
        'kam'=> 'Kamba',
        'bjj'=> 'Kanauji',
        'xnr'=> 'Kangri',
        'kn' => 'Kannada',
        'kaa'=> 'Kara-Kalpak',
        'krc'=> 'Karachay-Balkar',
        'krl'=> 'Karelian',
        'ks' => 'Kashmiri',
        'csb'=> 'Kashubian',
        'tkt'=> 'Kathoriya Tharu',
        'kk' => 'Kazakh',
        'kk_Arab' => 'Kazakh (Arabic)',
        'kvr'=> 'Kerinci',
        'kht'=> 'Khamti',
        'khn'=> 'Khandesi',
        'kha'=> 'Khasi',
        'km' => 'Khmer',
        'kjg'=> 'Khmu',
        'khw'=> 'Khowar',
        'ki' => 'Kikuyu',
        'kmb'=> 'Kimbundu',
        'krj'=> 'Kinaray-a',
        'rw' => 'Kinyarwanda',
        'kiu'=> 'Kirmanjki',
        'mwk'=> 'Kita Maninkakan',
        'thq'=> 'Kochila Tharu',
        'bkm'=> 'Kom',
        'kge'=> 'Komering',
        'kv' => 'Komi',
        'koi' => 'Komi-Permyak',
        'kg' => 'Kongo',
        'kok'=> 'Konkani',
        'ko' => 'Korean',
        'kfo'=> 'Koro',
        'bqv'=> 'Koro Wachi',
        'kos'=> 'Kosraean',
        'khq'=> 'Koyra Chiini',
        'ses'=> 'Koyraboro Senni',
        'kpe'=> 'Kpelle',
        'kri'=> 'Krio',
        'kj' => 'Kuanyama',
        'kfy'=> 'Kumaoni',
        'kum'=> 'Kumyk',
        'ku' => 'Kurdish',
        'ku_Arab' => 'Kurdish (Arabic)',
        'ky' => 'Kyrgyz',
        'ky_Arab' => 'Kyrgyz (Arabic)',
        'ky_Latin' => 'Kyrgyz (Latin)',
        'quc'=> 'Kʼicheʼ',
        'lad'=> 'Ladino',
        'lah'=> 'Lahnda',
        'lbe'=> 'Lak',
        'lki'=> 'Laki',
        'lkt'=> 'Lakota',
        'lmn'=> 'Lambadi',
        'ljp'=> 'Lampung Api',
        'lag'=> 'Langi',
        'laj'=> 'Lango [Uganda]',
        'lo' => 'Lao',
        'lgt'=> 'Latgalian',
        'la' => 'Latin',
        'lv' => 'Latvian',
        'lzz'=> 'Laz',
        'lep'=> 'Lepcha',
        'lez'=> 'Lezghian',
        'lij'=> 'Ligurian',
        'lif'=> 'Limbu',
        'li' => 'Limburgish',
        'ln' => 'Lingala',
        'lis'=> 'Lisu',
        'lzh'=> 'Literary Chinese',
        'lmo'=> 'Lombard',
        'ngl'=> 'Lomwe',
        'nds'=> 'Low German',
        'sli'=> 'Lower Silesian',
        'dsb'=> 'Lower Sorbian',
        'loz'=> 'Lozi',
        'khb'=> 'Lü',
        'lu' => 'Luba-Katanga',
        'lua'=> 'Luba-Lulua',
        'smj'=> 'Lule Sami',
        'luo'=> 'Luo',
        'lb' => 'Luxembourgish',
        'luy'=> 'Luyia',
        'ffm'=> 'Maasina Fulfulde',
        'mk' => 'Macedonian',
        'jmc'=> 'Machame',
        'mad'=> 'Madurese',
        'maf'=> 'Mafa',
        'mag'=> 'Magahi',
        'mdh'=> 'Maguindanaon',
        'vmf'=> 'Main-Franconian',
        'mai'=> 'Maithili',
        'mak'=> 'Makasar',
        'vmw'=> 'Makhuwa',
        'mgh'=> 'Makhuwa-Meetto',
        'kde'=> 'Makonde',
        'mg' => 'Malagasy',
        'ms' => 'Malay',
        'ms_Arab' => 'Malay (Arabic)',
        'ml' => 'Malayalam',
        'mt' => 'Maltese',
        'mdr'=> 'Mandar',
        'man'=> 'Mandingo',
        'man_Nkoo'=>    'Mandingo (N’Ko)',
        'mfv'=> 'Mandjak',
        'mni'=> 'Manipuri',
        'knf'=> 'Mankanya',
        'gv' => 'Manx',
        'mxc'=> 'Manyika',
        'mi' => 'Maori',
        'mr' => 'Marathi',
        'chm'=> 'Mari',
        'mh' => 'Marshallese',
        'mwr'=> 'Marwari',
        'myx'=> 'Masaaba',
        'mas'=> 'Masai',
        'mls'=> 'Masalit',
        'mzn'=> 'Mazanderani',
        'mgy'=> 'Mbunga',
        'byv'=> 'Medumba',
        'men'=> 'Mende',
        'tnr'=> 'Ménik',
        'mwv'=> 'Mentawai',
        'mer'=> 'Meru',
        'mgo'=> 'Metaʼ',
        'mtr'=> 'Mewari',
        'wtm'=> 'Mewati',
        'nan'=> 'Min Nan Chinese',
        'min'=> 'Minangkabau',
        'xmf'=> 'Mingrelian',
        'moh'=> 'Mohawk',
        'mdf'=> 'Moksha',
        'mnw'=> 'Mon',
        'lol'=> 'Mongo',
        'mn' => 'Mongolian',
        'mn_Mong'   =>  'Mongolian (Mongolian)',
        'moe'=> 'Montagnais',
        'crm'=> 'Moose Cree',
        'mfe'=> 'Morisyen',
        'ary'=> 'Moroccan Arabic',
        'mos'=> 'Mossi',
        'mro'=> 'Mru',
        'unx'=> 'Munda',
        'mua'=> 'Mundang',
        'unr'=> 'Mundari',
        'unr_Deva'  =>  'Mundari (Devanagari)',
        'mus'=> 'Muscogee',
        'ttt'=> 'Muslim Tat',
        'nqo'=> 'N’Ko',
        'ars'=> 'Najdi Arabic',
        'naq'=> 'Nama',
        'nsk'=> 'Naskapi',
        'na' => 'Nauru',
        'nv' => 'Navajo',
        'nqx'=> 'Naxi',
        'ndc'=> 'Ndau',
        'ng' => 'Ndonga',
        'wni'=> 'Ndzwani Comorian',
        'nap'=> 'Neapolitan',
        'zmi'=> 'Negeri Sembilan Malay',
        'ne' => 'Nepali',
        'new'=> 'Newari',
        'nij'=> 'Ngaju',
        'zdj'=> 'Ngazidja Comorian',
        'nnh'=> 'Ngiemboon',
        'jgo'=> 'Ngomba',
        'yrl'=> 'Nheengatu',
        'fuv'=> 'Nigerian Fulfulde',
        'pcm'=> 'Nigerian Pidgin',
        'noe'=> 'Nimadi',
        'niu'=> 'Niuean',
        'fia'=> 'Nobiin',
        'snf'=> 'Noon',
        'nd' => 'North Ndebele',
        'scs'=> 'North Slavey',
        'tts'=> 'Northeastern Thai',
        'crl'=> 'Northern East Cree',
        'frr'=> 'Northern Frisian',
        'hno'=> 'Northern Hindko',
        'kxm'=> 'Northern Khmer',
        'lrc'=> 'Northern Luri',
        'se' => 'Northern Sami',
        'nso'=> 'Northern Sotho	',
        'nod'=> 'Northern Thai',
        'nb' => 'Norwegian Bokmål',
        'nn' => 'Norwegian Nynorsk',
        'nus'=> 'Nuer',
        'nym'=> 'Nyamwezi',
        'ny' => 'Nyanja',
        'nyn'=> 'Nyankole',
        'tog'=> 'Nyasa Tonga',
        'nzi'=> 'Nzima',
        'oc' => 'Occitan',
        'or' => 'Odia',
        'om' => 'Oromo',
        'osa'=> 'Osage',
        'os' => 'Ossetic',
        'pfl'=> 'Palatine German',
        'pau'=> 'Palauan',
        'pam'=> 'Pampanga',
        'pag'=> 'Pangasinan',
        'pap'=> 'Papiamento',
        'kvx'=> 'Parkari Koli',
        'prd'=> 'Parsi-Dari',
        'ps' => 'Pashto',
        'mfa'=> 'Pattani Malay',
        'pdc'=> 'Pennsylvania German',
        'fa' => 'Persian',
        'pcd'=> 'Picard',
        'pms'=> 'Piedmontese',
        'crk'=> 'Plains Cree',
        'pdt'=> 'Plautdietsch',
        'pon'=> 'Pohnpeian',
        'pko'=> 'Pökoot',
        'pl' => 'Polish',
        'pnt'=> 'Pontic',
        'pt' => 'Portuguese',
        'pa' => 'Punjabi',
        'pa_Arab'   =>  'Punjabi (Arabic)',
        'puu'=> 'Punu',
        'qu' => 'Quechua',
        'raj'=> 'Rajasthani',
        'rjs'=> 'Rajbanshi',
        'thr'=> 'Rana Tharu',
        'rkt'=> 'Rangpuri',
        'rej'=> 'Rejang',
        'rcf'=> 'Réunion Creole French',
        'ria'=> 'Riang [India]',
        'rif'=> 'Riffian',
        'rif_Latn'  =>  'Riffian (Latin)',
        'bto'=> 'Rinconada Bikol',
        'rgn'=> 'Romagnol',
        'ro' => 'Romanian',
        'rm' => 'Romansh',
        'rof'=> 'Rombo',
        'rng'=> 'Ronga',
        'rtm'=> 'Rotuman',
        'rug'=> 'Roviana',
        'rn' => 'Rundi',
        'ru' => 'Russian',
        'rue'=> 'Rusyn',
        'rwk'=> 'Rwa',
        'sav'=> 'Saafi-Saafi',
        'sck'=> 'Sadri',
        'saf'=> 'Safaliba',
        'ssy'=> 'Saho',
        'sah'=> 'Sakha',
        'saq'=> 'Samburu',
        'sm' => 'Samoan',
        'sgs'=> 'Samogitian',
        'sxn'=> 'Sangir',
        'sg' => 'Sango',
        'sbp'=> 'Sangu',
        'sa' => 'Sanskrit',
        'sat'=> 'Santali',
        'skr'=> 'Saraiki',
        'sas'=> 'Sasak',
        'sdc'=> 'Sassarese Sardinian',
        'stq'=> 'Saterland Frisian',
        'saz'=> 'Saurashtra',
        'sco'=> 'Scots',
        'gd' => 'Scottish Gaelic',
        'sly'=> 'Selayar',
        'seh'=> 'Sena',
        'sr' => 'Serbian',
        'sr_Latn'   =>  'Serbian (Latin)',
        'srr'=> 'Serer',
        'sei'=> 'Seri',
        'crs'=> 'Seselwa Creole French',
        'ksb'=> 'Shambala',
        'shn'=> 'Shan',
        'swv'=> 'Shekhawati',
        'xsr'=> 'Sherpa',
        'sn' => 'Shona',
        'ii' => 'Sichuan Yi',
        'scn'=> 'Sicilian',
        'sid'=> 'Sidamo',
        'szi'=> 'Silesian',
        'sd' => 'Sindhi',
        'sd_Deva'   =>  'Sindhi (Devanagari)',
        'si' => 'Sinhala',
        'rmo'=> 'Sinte Romani',
        'srx'=> 'Sirmauri',
        'sms'=> 'Skolt Sami',
        'den'=> 'Slave',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'xog'=> 'Soga',
        'so' => 'Somali',
        'snk'=> 'Soninke',
        'nr' => 'South Ndebele',
        'alt'=> 'Southern Altai',
        'crj'=> 'Southern East Cree',
        'hnd'=> 'Southern Hindko',
        'sdh'=> 'Southern Kurdish',
        'luz'=> 'Southern Luri',
        'sma'=> 'Southern Sami',
        'st' => 'Southern Sotho',
        'sou'=> 'Southern Thai',
        'es' => 'Spanish',
        'srn'=> 'Sranan Tongo',
        'zgh'=> 'Standard Moroccan Tamazigh',
        'suk'=> 'Sukuma',
        'su' => 'Sundanese',
        'sus'=> 'Susu',
        'swg'=> 'Swabian',
        'sw' => 'Swahili',
        'csw'=> 'Swampy Cree',
        'ss' => 'Swati',
        'sv' => 'Swedish',
        'gsw'=> 'Swiss German',
        'syl'=> 'Sylheti',
        'syr'=> 'Syriac',
        'shi'=> 'Tachelhit',
        'shi_Latn'  =>  'Tachelhit (Latin)',
        'rob'=> 'Tae',
        'tbw'=> 'Tagbanwa',
        'ty' => 'Tahitian',
        'tdd'=> 'Tai Nüa',
        'dav'=> 'Taita',
        'tg' => 'Tajik',
        'tg_Arab'   =>  'Tajik (Arabic)',
        'tly'=> 'Talysh',
        'tmh'=> 'Tamashek',
        'ta' => 'Tamil',
        'trv'=> 'Taroko',
        'twq'=> 'Tasawaq',
        'tt' => 'Tatar',
        'tsg'=> 'Tausug',
        'rmu'=> 'Tavringer Romani',
        'te' => 'Telugu',
        'teo'=> 'Teso',
        'tet'=> 'Tetum',
        'th' => 'Thai',
        'tdh'=> 'Thulung',
        'bo' => 'Tibetan',
        'tig'=> 'Tigre',
        'ti' => 'Tigrinya',
        'tem'=> 'Timne',
        'tiv'=> 'Tiv',
        'tpi'=> 'Tok Pisin',
        'tkl'=> 'Tokelau',
        'lbw'=> 'Tolaki',
        'dtm'=> 'Tomo Kan Dogon',
        'to' => 'Tongan',
        'ttj'=> 'Tooro',
        'fit'=> 'Tornedalen Finnish',
        'zh_Haut'   =>  'Traditional Chinese',
        'tkr'=> 'Tsakhur',
        'tsd'=> 'Tsakonian',
        'tsj'=> 'Tshangla',
        'ts' => 'Tsonga',
        'tn' => 'Tswana',
        'tcy'=> 'Tulu',
        'tum'=> 'Tumbuka',
        'aeb'=> 'Tunisian Arabic',
        'tr' => 'Turkish',
        'tk' => 'Turkmen',
        'tru'=> 'Turoyo',
        'tvl'=> 'Tuvalu',
        'tyv'=> 'Tuvinian',
        'kcg'=> 'Tyap',
        'aoz'=> 'Uab Meto',
        'udm'=> 'Udmurt',
        'uk' => 'Ukrainian',
        'uli'=> 'Ulithian',
        'umb'=> 'Umbundu',
        'und'=> 'nknown language',
        'hsb'=> 'Upper Sorbian',
        'ur' => 'Urdu',
        'ug' => 'Uyghur',
        'ug_Cyrl'   =>  'Uyghur (Cyrillic)',
        'uz' => 'Uzbek',
        'uz_Arav'   =>  'Uzbek (Arabic)',
        'uz_Cryl'   =>  'Uzbek (Cyrillic)',
        'vai'=>  'Vai',
        'vai_Latn'  =>  'Vai (Latin)',
        've' => 'Venda',
        'vec'=> 'Venetian',
        'vep'=> 'Veps',
        'vi' => 'Vietnamese',
        'vic'=> 'Virgin Islands Creole English',
        'vri'=> 'Võro',
        'vot'=> 'Votic',
        'vun'=> 'Vunjo',
        'wbq'=> 'Waddar',
        'kxp'=> 'Wadiyara Koli',
        'wbr'=> 'Wagdi',
        'wls'=> 'Wallisian',
        'wa' => 'Walloon',
        'wae'=> 'Walser',
        'war'=> 'Waray',
        'wbp'=> 'Warlpiri',
        'guc'=> 'Wayuu',
        'cy' => 'Welsh',
        'vls'=> 'West Flemish',
        'bgn'=> 'Western Balochi',
        'cja'=> 'Western Cham',
        'nhw'=> 'Western Huasteca Nahuat',
        'lcp'=> 'Western Lawa',
        'mrd'=> 'Western Magar',
        'mrj'=> 'Western Mari',
        'tdg'=> 'Western Tamang',
        'wal'=> 'Wolaytta',
        'wo' => 'Wolof',
        'wuu'=> 'Wu Chinese',
        'kao'=> 'Xaasongaxango',
        'xav'=> 'Xavánte',
        'hsn'=> 'Xiang Chinese',
        'yav'=> 'Yangben',
        'yao'=> 'Yao',
        'yap'=> 'Yapese',
        'ybb'=> 'Yemba',
        'yi' => 'Yiddish',
        'yo' => 'Yoruba',
        'yua'=> 'Yucateco',
        'zag'=> 'Zaghawa',
        'dje'=> 'Zarma',
        'zza'=> 'Zaza',
        'zea'=> 'Zeelandic',
        'gbz'=> 'Zoroastrian Dari',
        'zu' => 'Zulu'
    );

    $tags = get_tags(array('hide_empty' => false));
?>

<div class="profile">
    <?php if(bp_is_my_profile() && bp_current_action() == 'edit'): ?>
        <?php 
            // Get current user
            $user = wp_get_current_user()->data;

            // Get default user meta data
            $meta = get_user_meta($user->ID);

            if(isset($meta['community-meta-fields']) && isset($meta['community-meta-fields'][0])) {
                $community_fields = unserialize($meta['community-meta-fields'][0]);
            } else {
                $community_fields = false;
            }

            $form = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : false;

            if($form && isset($form['tags'])) {
                $form_tags = array_filter(explode(',', $form['tags']));
            } else {

                if($community_fields && isset($community_fields['tags'])) {
                    $form_tags = array_filter(explode(',', $community_fields['tags']));
                } else {
                    $form_tags = Array();
                }
            }

            do_action('bp_before_edit_member_page');

            $complete = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete']) && $_POST['complete'] === true) ? true :  false;
            $edit = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit']) && $_POST['edit'] === true) ? true :  false;
            $updated_username = isset($form['username']) ? $form['username'] : false;

            include("{$template_dir}/buddypress/members/single/edit.php");
        ?>
    <?php else: ?>
        <?php 
            // Public profile
            $user_id = bp_displayed_user_id();
            $user = get_user_by('ID', $user_id);
            
            $logged_in = mozilla_is_logged_in();
            $current_user = wp_get_current_user()->data;
            
            $is_me = $logged_in && intval($current_user->ID) === intval($user->ID);
        
            $info = mozilla_get_user_info($current_user, $user, $logged_in);
            include("{$template_dir}/buddypress/members/single/profile.php");           
        ?>
    <?php endif; ?>
</div>	
