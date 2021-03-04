<?php


namespace App\Service\Governor;


class EquipmentNames
{
    const UID_SPECIAL_TALENT_LEADERSHIP = 'st_l';
    const UID_SPECIAL_TALENT_INF = 'st_i';
    const UID_SPECIAL_TALENT_CAV = 'st_c';
    const UID_SPECIAL_TALENT_ARCHER = 'st_a';
    const UID_SPECIAL_TALENT_INTEGRATION = 'st_it';

    const UID_HW_WH = 'hw_wh';
    const UID_HW_R = 'hw_r';
    const UID_HW_HA = 'hw_ha';
    const UID_HW_A = 'hw_a';
    const UID_HW_T = 'hw_t';
    const UID_HW_B = 'hw_b';

    const UID_EE_S = 'ee_s';
    const UID_EE_GH = 'ee_gh';
    const UID_EE_P = 'ee_p';
    const UID_EE_V = 'ee_v';
    const UID_EE_G = 'ee_g';
    const UID_EE_SB = 'ee_sb';

    const UID_DB_B = 'db_b';
    const UID_DB_H = 'db_h';
    const UID_DB_P = 'db_p';
    const UID_DB_V = 'db_v';
    const UID_DB_T = 'db_t';
    const UID_DB_BT = 'db_bt';

    const UID_W_SD = 'w_sd';
    const UID_W_HSM = 'w_hsm';
    const UID_W_HB = 'w_hb';

    const UID_H_PK = 'h_pk';
    const UID_H_HC = 'h_hc';
    const UID_H_AMN = 'h_amn';

    const UID_C_SLR = 'c_slr';
    const UID_C_HC = 'c_hc';
    const UID_C_MW = 'c_mw';

    const UID_G_IC = 'g_ic';
    const UID_G_SG = 'g_sg';
    const UID_G_NC = 'g_nc';

    const UID_L_WG = 'l_wg';
    const UID_L_AD = 'l_ad';
    const UID_L_EN = 'l_en';

    const UID_B_SR = 'b_sr';
    const UID_B_CB = 'b_cb';
    const UID_B_MC = 'b_mc';

    const UID_A_LC = 'a_lc';
    const UID_A_PEN = 'a_pen';
    const UID_A_RD = 'a_rd';
    const UID_A_GG = 'a_gg';
    const UID_A_HF = 'a_hf';
    const UID_A_CD = 'a_cd';
    const UID_A_MW = 'a_mw';
    const UID_A_V = 'a_v';
    const UID_A_KWD = 'a_kwd';
    const UID_A_SC = 'a_sc';
    const UID_A_D = 'a_d';
    const UID_A_ST = 'a_st';
    const UID_A_WS = 'a_ws';
    const UID_A_AS = 'a_as';
    const UID_A_CL = 'a_cl';
    const UID_A_SVT = 'a_svt';

    const ALL = [
        self::UID_HW_WH => 'War Helm of the Hellish Wasteland',
        self::UID_HW_R => 'Rifle of the Hellish Wasteland',
        self::UID_HW_HA => 'Heavy Armor of the Hellish Wasteland',
        self::UID_HW_A => 'Armband of the Hellish Wasteland',
        self::UID_HW_T => 'Tassets of the Hellish Wasteland',
        self::UID_HW_B => 'Boots of the Hellish Wasteland',
        self::UID_EE_S => 'Shield of the Eternal Empire',
        self::UID_EE_GH => 'Gold Helm of the Eternal Empire',
        self::UID_EE_P => 'Plate of the Eternal Empire',
        self::UID_EE_V => 'Vambraces of the Eternal Empire',
        self::UID_EE_G => 'Greaves of the Eternal Empire',
        self::UID_EE_SB => 'Sturdy Boots of the Eternal Empire',
        self::UID_DB_B => 'Dragon\'s Breath Bow',
        self::UID_DB_H => 'Dragon\'s Breath Helm',
        self::UID_DB_P => 'Dragon\'s Breath Plate',
        self::UID_DB_V => 'Dragon\'s Breath Vambraces',
        self::UID_DB_T => 'Dragon\'s Breath Tassets',
        self::UID_DB_BT => 'Dragon\'s Breath Boots',
        self::UID_W_SD => 'Sacred Dominion',
        self::UID_W_HSM => 'Hammer of the Sun and Moon',
        self::UID_W_HB => 'The Hydra\'s Blast',
        self::UID_H_PK => 'Pride of the Khan',
        self::UID_H_HC => 'Helm of the Conqueror',
        self::UID_H_AMN => 'Ancestral Mask of Knight',
        self::UID_C_SLR => 'Shadow Legion\'s Retribution',
        self::UID_C_HC => 'Hope Cloak',
        self::UID_C_MW => 'The Milky Way',
        self::UID_G_IC => 'Ian\'s Choice',
        self::UID_G_SG => 'Sacred Grips',
        self::UID_G_NC => 'Navar\'s Control',
        self::UID_L_WG => 'Cape of the War God',
        self::UID_L_AD => 'Ash of the Dawn',
        self::UID_L_EN => 'Eternal Night',
        self::UID_B_SR => 'Shio\'s Return',
        self::UID_B_CB => 'Commander\'s Boots',
        self::UID_B_MC => 'Mountain Crushers',
        self::UID_A_LC => 'Scolas\' Lucky Coin',
        self::UID_A_PEN => 'Pendant of Eternal Night',
        self::UID_A_RD => 'Ring of Doom',
        self::UID_A_GG => 'Greatest Glory',
        self::UID_A_HF => 'Horn of Fury',
        self::UID_A_CD => 'Concealed Dagger',
        self::UID_A_MW => 'Mora\'s Web',
        self::UID_A_V => 'Vengeance',
        self::UID_A_KWD => 'Karuak\'s War Drums',
        self::UID_A_SC => 'Seth\'s Call',
        self::UID_A_D => 'Delane\'s Amulet',
        self::UID_A_ST => 'Silent Trial',
        self::UID_A_WS => 'Wind Scars',
        self::UID_A_AS => 'Ancient Strategems',
        self::UID_A_CL => 'Call of the Loyal',
        self::UID_A_SVT => 'Savage Totem',
    ];

    const SPECIAL_TALENTS = [
        self::UID_SPECIAL_TALENT_LEADERSHIP => 'Leadership',
        self::UID_SPECIAL_TALENT_INF => 'Infantry',
        self::UID_SPECIAL_TALENT_CAV => 'Cavalry',
        self::UID_SPECIAL_TALENT_ARCHER => 'Archer',
        self::UID_SPECIAL_TALENT_INTEGRATION => 'Integration',
    ];
}