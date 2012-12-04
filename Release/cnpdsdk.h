/******************************************************************************
*                                                                             *
* cnpdsdk.h - Canon Printer/Fax Driver SDK Version 1.60                       *
*                                                                             *
* Copyright (C) 2001-2002 Canon Inc.  All rights reserved.                    *
*                                                                             *
******************************************************************************/

#ifndef INC_CNPDSDK
#define INC_CNPDSDK

/*===============================================================*
 * CanonDeviceModeEx                                             *
 *===============================================================*/
/*----- SDK Version -----*/
#define CDM_SDK_VERSION                 0x0001003CUL
          /* CanonDeviceModeEx SDK Version */
#define CDM_SDK_VERSION100              0x00010000UL
          /* CanonDeviceModeEx SDK Version 1.00 */
#define CDM_SDK_VERSION110              0x0001000AUL
          /* CanonDeviceModeEx SDK Version 1.10 */
#define CDM_SDK_VERSION111              0x0001000BUL
          /* CanonDeviceModeEx SDK Version 1.11 */
#define CDM_SDK_VERSION113              0x0001000DUL
          /* CanonDeviceModeEx SDK Version 1.13 */
#define CDM_SDK_VERSION120              0x00010014UL
          /* CanonDeviceModeEx SDK Version 1.20 */
#define CDM_SDK_VERSION160              0x0001003CUL
          /* CanonDeviceModeEx SDK Version 1.60 */

/*----- Return Code -----*/
#define CDM_RET_OK                      0UL     /* Success         */
#define CDM_RET_FAILED                  1UL     /* Error           */
#define CDM_RET_NOTSUPPORTED            2UL     /* Unsupported     */
#define CDM_RET_NOTAVAILABLE            3UL     /* Unavailable     */
#define CDM_RET_BADPARAMETER            6UL     /* Wrong Parameter */

/*----- Function ID -----*/
#define CDM_FUNC_GETSIZE                0UL     /* Gets the size */
#define CDM_FUNC_GETCOUNT               1UL     /* Gets the number of parameters */
#define CDM_FUNC_GETITEM                2UL     /* Gets the driver's setup */
#define CDM_FUNC_SETITEM                3UL     /* Changes the driver's setup */
#define CDM_FUNC_ENUMITEMS              4UL     /* Enumerates the parameters */
#define CDM_FUNC_FACTORYRESET           6UL     /* Restores the default value    */

/*----- Item ID -----*/
#define CDM_ITEM_BASE                   65536UL
#define CDM_ITEM_VERSION                CDM_ITEM_BASE
      /* The version number of CanonDeviceModeEx interface */
#define CDM_ITEM_DEVMODE                (CDM_ITEM_BASE+1UL)
          /* Resets the DEVMODE structure  */
#define CDM_ITEM_OUTPUTDESTINATION      (CDM_ITEM_BASE+2UL)
          /* The processing method of a job */
#define CDM_ITEM_STRETCHSIZE            (CDM_ITEM_BASE+3UL)
          /* Scaling rate */
#define CDM_ITEM_COLORDEPTH             (CDM_ITEM_BASE+4UL)
          /* Gradation */
#define CDM_ITEM_GLYPH                  (CDM_ITEM_BASE+5UL)
          /* Use glyph index fonts */
#define CDM_ITEM_LAYOUT                 (CDM_ITEM_BASE+100UL)
          /* Page layout */
#define CDM_ITEM_LAYOUTORDER            (CDM_ITEM_BASE+101UL)
          /* Page Order */
#define CDM_ITEM_STAMPPRINT             (CDM_ITEM_BASE+102UL)
          /* Watermark */
#define CDM_ITEM_STAMPSTRINGS           (CDM_ITEM_BASE+103UL)
          /* The attribute of stamp printing */
#define CDM_ITEM_2SIDEDPRINT            (CDM_ITEM_BASE+104UL)
          /* 2-sided Printing */
#define CDM_ITEM_STAPLEPOS              (CDM_ITEM_BASE+106UL)
          /* Staple Position */
#define CDM_ITEM_BOOKLETPRINT           (CDM_ITEM_BASE+107UL)
          /* Booklet Printing */
#define CDM_ITEM_CENTERFOLD             (CDM_ITEM_BASE+108UL)
          /* Saddle Stitch */
#define CDM_ITEM_INTERLEAFPRINT         (CDM_ITEM_BASE+110UL)
          /* Print on Interleaf Sheets */
#define CDM_ITEM_GRAPHICMODE            (CDM_ITEM_BASE+111UL)
          /* Graphics Mode */
#define CDM_ITEM_BINDDIRECT             (CDM_ITEM_BASE+114UL)
          /* Binding Location */
#define CDM_ITEM_BINDMARGIN             (CDM_ITEM_BASE+115UL)
          /* Gutter */
#define CDM_ITEM_COLORMODE              (CDM_ITEM_BASE+116UL)
          /* Color Mode */
#define CDM_ITEM_FINISHER               (CDM_ITEM_BASE+120UL)
          /* Output Options */
#define CDM_ITEM_STRETCH                (CDM_ITEM_BASE+122UL)
          /* Manual Scaling */
#define CDM_ITEM_FAXMODE                (CDM_ITEM_BASE+123UL)
          /* Queries if it is a fax driver */
#define CDM_ITEM_PROFILE                (CDM_ITEM_BASE+125UL)
          /* Profile */
#define CDM_ITEM_OUTPUTPAPER            (CDM_ITEM_BASE+126UL)
          /* Output Size */
#define CDM_ITEM_FINISHING              (CDM_ITEM_BASE+127UL)
          /* Finishing */
#define CDM_ITEM_OUTPUTBIN              (CDM_ITEM_BASE+128UL)
          /* Paper Output */
#define CDM_ITEM_STARTBIN               (CDM_ITEM_BASE+129UL)
          /* Specify Start Bin */
#define CDM_ITEM_PAPERSELECTION         (CDM_ITEM_BASE+130UL)
          /* Paper Selection */
#define CDM_ITEM_MEDIATYPE              (CDM_ITEM_BASE+131UL)
          /* Paper Type */
#define CDM_ITEM_COLORHALFTONE          (CDM_ITEM_BASE+132UL)
          /* Color Halftones */
#define CDM_ITEM_MONOHALFTONE           (CDM_ITEM_BASE+133UL)
          /* Monochrome Halftones */
#define CDM_ITEM_SAVETONER              (CDM_ITEM_BASE+135UL)
          /* Toner Save */

/*---------------------------------------------------------*/
#define CDM_ITEM_BINDMARGIN_INCH        (CDM_ITEM_BASE+20UL)
          /* Unit Appointment of Bind (PCL only) */
#define CDM_ITEM_INSERTION              (CDM_ITEM_BASE+21UL)
          /* Insertion Unit (PCL only) */
#define CDM_ITEM_RENDERING              (CDM_ITEM_BASE+22UL)
          /* Rendering Rate (PCL only) */
#define CDM_ITEM_PRIORITY               (CDM_ITEM_BASE+23UL)
          /* The processing method of rasterizing */
#define CDM_ITEM_BANDING                (CDM_ITEM_BASE+24UL)
          /* Banding (PCL only) */
#define CDM_ITEM_TEXTGRAPHICS           (CDM_ITEM_BASE+25UL)
          /* Print Text as Graphics (PCL only) */
#define CDM_ITEM_USEPRTFONT             (CDM_ITEM_BASE+26UL)
          /* Use Printer Font (PCL only) */
#define CDM_ITEM_TTMODE                 (CDM_ITEM_BASE+27UL)
          /* TrueType Mode (PCL only) */
#define CDM_ITEM_REFINE                 (CDM_ITEM_BASE+28UL)
          /* Control of image smoothing processing (PCL only) */
#define CDM_ITEM_MULTIPAGE              (CDM_ITEM_BASE+29UL)
          /* Control of multi-page size or multi-orientation (PCL only) */
#define CDM_ITEM_LINEREFINE             (CDM_ITEM_BASE+30UL)
          /* Control in thin line emphasis mode (PCL only) */
#define CDM_ITEM_EMF                    (CDM_ITEM_BASE+31UL)
          /* Control of an EMF spool (PCL only) */
#define CDM_ITEM_REVPRINT               (CDM_ITEM_BASE+32UL)
          /* Control of the order of reverse (PCL only) */
#define CDM_ITEM_EDGING                 (CDM_ITEM_BASE+33UL)
          /* Control of a page frame function (PCL only) */
#define CDM_ITEM_PRINTDATE              (CDM_ITEM_BASE+34UL)
          /* Print Date */
#define CDM_ITEM_PRINTUSER              (CDM_ITEM_BASE+35UL)
          /* Print User Name */
#define CDM_ITEM_PRINTPAGENO            (CDM_ITEM_BASE+36UL)
          /* Print Page Number */

/*---------------------------------------------------------*/
#define CDM_ITEM_PRINTQUALITY           (CDM_ITEM_BASE+90UL)
          /* Print quality (GARO only) */
#define CDM_ITEM_TIMETODRY              (CDM_ITEM_BASE+91UL)
          /* Ink dryness time (GARO only) */
#define CDM_ITEM_PAPERCUT               (CDM_ITEM_BASE+92UL)
          /* Paper cut (GARO only) */
#define CDM_ITEM_MEDIATYPEEX            (CDM_ITEM_BASE+93UL)
          /* Media type (GARO only) */


/*---------------------------------------------------------*/
#define CDM_ITEM_FAX_FILENAMESEED       (CDM_ITEM_BASE+500UL)
          /* seed to generate a data file name */
#define CDM_ITEM_FAX_COVERPAGETEMPLATE  (CDM_ITEM_BASE+502UL)
          /* cover sheet template */
#define CDM_ITEM_FAX_DESCRIPTIONMASK    (CDM_ITEM_BASE+503UL)
          /* FAX UI Flags */
#define CDM_ITEM_FAX_BROADCASTSPEC      (CDM_ITEM_BASE+510UL)
          /* broadcast for device */
#define CDM_ITEM_FAX_PREFIXNUMBER       (CDM_ITEM_BASE+512UL)
          /* outside dialing prefix */
#define CDM_ITEM_FAX_TRANSMODE          (CDM_ITEM_BASE+513UL)
          /* resolution of the fax */
#define CDM_ITEM_FAX_UIOPEN             (CDM_ITEM_BASE+522UL)
          /* Fax UI mode */
#define CDM_ITEM_FAX_REGISTTEMPLATE     (CDM_ITEM_BASE+524UL)
          /* regist cover sheet template file */
#define CDM_ITEM_FAX_DELETETEMPLATE     (CDM_ITEM_BASE+525UL)
          /* delete cover sheet template */

#define CDM_FALSE                       0UL     /* FALSE */
#define CDM_TRUE                        1UL     /* TRUE  */

/* CDM_ITEM_OUTPUTDESTINATION */
#define CDM_OUTPUTDEST_PRINT            0       /* Print            */
#define CDM_OUTPUTDEST_EDITPREVIEW      1       /* Edit and Preview */
#define CDM_OUTPUTDEST_INTERRUPT        2       /* Interrupt Print  */
#define CDM_OUTPUTDEST_PROMOTE          6       /* Promote Print    */

/* CDM_ITEM_LAYOUT */
#define CDM_NUP_MODE_1                  0       /* 1 Page per Sheet  */
#define CDM_NUP_MODE_2                  1       /* 2 Page per Sheet  */
#define CDM_NUP_MODE_4                  2       /* 4 Page per Sheet  */
#define CDM_NUP_MODE_6                  10      /* 6 Page per Sheet  */
#define CDM_NUP_MODE_8                  3       /* 8 Page per Sheet  */
#define CDM_NUP_MODE_9                  4       /* 9 Page per Sheet  */
#define CDM_NUP_MODE_16                 5       /* 16 Page per Sheet */
#define CDM_POSTER_2X2                  20      /* Poster [2 x 2]    */
#define CDM_POSTER_3X3                  21      /* Poster [3 x 3]    */
#define CDM_POSTER_4X4                  22      /* Poster [4 x 4]    */

/* CDM_ITEM_LAYOUTORDER */
#define CDM_NUPORDER_LR                 0       /* Left to Right     */
#define CDM_NUPORDER_RL                 1       /* Right to Left     */
#define CDM_NUPORDER_UD                 2       /* Top to Bottom     */
#define CDM_NUPORDER_DU                 3       /* Bottom to Top     */
#define CDM_NUPORDER_LUR                4       /* Across from Left  */
#define CDM_NUPORDER_RUL                5       /* Across from Right */
#define CDM_NUPORDER_LUD                6       /* Down from Left    */
#define CDM_NUPORDER_RUD                7       /* Down from Right   */

/* CDM_ITEM_STRETCH */
#define CDM_STRETCH_OFF                 0       /* Disable Manual Scaling */
#define CDM_STRETCH_ON                  1       /* Enable Manual Scaling  */

/* CDM_ITEM_STAMPPRINT */
#define CDM_STAMPPRINT_OFF              0       /* Disable Watermark */
#define CDM_STAMPPRINT_ON               1       /* Enable Watermark  */

/* CDM_ITEM_2SIDEDPRINT */
#define CDM_2SIDEPRINT_OFF              0       /* Disable 2-sided Printing */
#define CDM_2SIDEPRINT_ON               1       /* Enable 2-sided Printing  */

/* CDM_ITEM_BOOKLETPRINT */
#define CDM_BOOKLET_OFF                 0       /* Disable Booklet Printing */
#define CDM_BOOKLET_ON                  1       /* Enable Booklet Printing  */

/* CDM_ITEM_CENTERFOLD */
#define CDM_CENTERFOLD_OFF              0       /* Disable Saddle Stitch */
#define CDM_CENTERFOLD_ON               1       /* Enable Saddle Stitch  */

/* CDM_ITEM_BINDDIRECT */
#define CDM_BINDDIRECT_LONGLT           0       /* Long Edge [Left]/[Top]     */
#define CDM_BINDDIRECT_LONGRB           1       /* Long Edge [Right]/[Bottom] */
#define CDM_BINDDIRECT_SHORTTR          2       /* Short Edge [Top]/[Right]   */
#define CDM_BINDDIRECT_SHORTBL          3       /* Short Edge [Bottom]/[Left] */

/* CDM_ITEM_FINISHING */
#define CDM_FINISHING_OFF               0           /* Off        */
#define CDM_FINISHING_COLLATE           1           /* Sort       */
#define CDM_FINISHING_GROUP             2           /* Group      */
#define CDM_FINISHING_STAPLE            3           /* Staple     */
#define CDM_FINISHING_JOBOFFSET         4           /* Offset     */
#define CDM_FINISHING_FACEUP            5           /* Face Up    */
#define CDM_FINISHING_ROTATE            0x00010000  /* Rotate     */
#define CDM_FINISHING_PUNCH             0x00020000  /* Hole Punch */
#define CDM_FINISHING_ZFOLD             0x00040000  /* Z-fold     */

/* CDM_ITEM_OUTPUTBIN */
#define CDM_FIXBIN_AUTO                 1       /* Auto                           */
#define CDM_FIXBIN_MAINTRAY             2       /* Output Tray                    */
#define CDM_FIXBIN_SUBTRAY              3       /* Sub-output Tray                */
#define CDM_FIXBIN_NONSORT              4       /* Non-Sort Bin                   */
#define CDM_FIXBIN_TRAY1                6       /* Upper Tray                     */
#define CDM_FIXBIN_BIN1                 7       /* Bin 1                          */
#define CDM_FIXBIN_BIN2                 8       /* Bin 2                          */
#define CDM_FIXBIN_BIN3                 9       /* Bin 3                          */
#define CDM_FIXBIN_BIN4                 10      /* Bin 4                          */
#define CDM_FIXBIN_BIN5                 11      /* Bin 5                          */
#define CDM_FIXBIN_BIN6                 12      /* Bin 6                          */
#define CDM_FIXBIN_BIN7                 13      /* Bin 7                          */
#define CDM_FIXBIN_BIN8                 14      /* Bin 8                          */
#define CDM_FIXBIN_BIN9                 15      /* Bin 9                          */
#define CDM_FIXBIN_BIN10                16      /* Bin 10                         */
#define CDM_FIXBIN_BIN11                17      /* Bin 11                         */
#define CDM_FIXBIN_BIN12                18      /* Bin 12                         */
#define CDM_FIXBIN_BIN13                19      /* Bin 13                         */
#define CDM_FIXBIN_BIN14                20      /* Bin 14                         */
#define CDM_FIXBIN_BIN15                21      /* Bin 15                         */
#define CDM_FIXBIN_BIN16                22      /* Bin 16                         */
#define CDM_FIXBIN_BIN17                23      /* Bin 17                         */
#define CDM_FIXBIN_BIN18                24      /* Bin 18                         */
#define CDM_FIXBIN_BIN19                25      /* Bin 19                         */
#define CDM_FIXBIN_BIN20                26      /* Bin 20                         */
#define CDM_FIXBIN_FACEDOWN             40      /* Face-down Tray (PCL only)      */
#define CDM_FIXBIN_SORTERTOPBIN         41      /* Sorter Top Bin (PCL only)      */
#define CDM_FIXBIN_OFF                  42      /* Start Bin is already specified (PCL only) */
#define CDM_FIXBIN_FACEUP               43      /* Face-up Tray (PCL only)                   */

/* CDM_ITEM_STARTBIN */
#define CDM_STARTBIN_AUTO               0       /* Auto                           */
#define CDM_STARTBIN_BIN1               1       /* Bin 1                          */
#define CDM_STARTBIN_BIN2               2       /* Bin 2                          */
#define CDM_STARTBIN_BIN3               3       /* Bin 3                          */
#define CDM_STARTBIN_BIN4               4       /* Bin 4                          */
#define CDM_STARTBIN_BIN5               5       /* Bin 5                          */
#define CDM_STARTBIN_BIN6               6       /* Bin 6                          */
#define CDM_STARTBIN_BIN7               7       /* Bin 7                          */
#define CDM_STARTBIN_BIN8               8       /* Bin 8                          */
#define CDM_STARTBIN_BIN9               9       /* Bin 9                          */
#define CDM_STARTBIN_BIN10              10      /* Bin 10                         */
#define CDM_STARTBIN_BIN11              11      /* Bin 11                         */
#define CDM_STARTBIN_BIN12              12      /* Bin 12                         */
#define CDM_STARTBIN_BIN13              13      /* Bin 13                         */
#define CDM_STARTBIN_BIN14              14      /* Bin 14                         */
#define CDM_STARTBIN_BIN15              15      /* Bin 15                         */
#define CDM_STARTBIN_BIN16              16      /* Bin 16                         */
#define CDM_STARTBIN_BIN17              17      /* Bin 17                         */
#define CDM_STARTBIN_BIN18              18      /* Bin 18                         */
#define CDM_STARTBIN_BIN19              19      /* Bin 19                         */
#define CDM_STARTBIN_BIN20              20      /* Bin 20                         */
#define CDM_STARTBIN_OFF                30      /* OutputBin is already specified (PCL only) */

/* CDM_ITEM_STAPLEPOS */
#define CDM_SLOCATION_TL                1       /* Upper Left  */
#define CDM_SLOCATION_TC                2       /* Top         */
#define CDM_SLOCATION_TR                3       /* Upper Right */
#define CDM_SLOCATION_ML                4       /* Left        */
#define CDM_SLOCATION_MR                5       /* Right       */
#define CDM_SLOCATION_BL                6       /* Lower Left  */
#define CDM_SLOCATION_BC                7       /* Bottom      */
#define CDM_SLOCATION_BR                8       /* Lower Right */

/* CDM_ITEM_PAPERSELECTION */
#define CDM_PAPERSELECTION_ALL          0       /* Same Paper for All Pages                      */
#define CDM_PAPERSELECTION_FOL          1       /* Different for First, Others, and Last         */
#define CDM_PAPERSELECTION_FSOL         2       /* Different for First, Second, Others, and Last */
#define CDM_PAPERSELECTION_COVER        3       /* Different for Cover and Others                */
#define CDM_PAPERSELECTION_OHP          4       /* Transparency Interleaving                     */

/* CDM_ITEM_INTERLEAFPRINT */
#define CDM_INTERLEAFPRINT_OFF          0       /* Disable Print on Interleaf Sheets */
#define CDM_INTERLEAFPRINT_ON           1       /* Enable Print on Interleaf Sheets  */

/* CDM_ITEM_MEDIATYPE */
#define CDM_MEDIATYPE_PANEL             0       /* Printer Default  */
#define CDM_MEDIATYPE_PLANE             1       /* Plain Paper      */
#define CDM_MEDIATYPE_THICKNESS         2       /* Heavy Paper      */
#define CDM_MEDIATYPE_TRANSPARENCY      3       /* Transparencies   */
#define CDM_MEDIATYPE_COATED            4       /* Glossy Films     */
#define CDM_MEDIATYPE_PLANEL            5       /* Plain Paper Low  */
#define CDM_MEDIATYPE_THICKNESSH        6       /* Heavy Paper High */
#define CDM_MEDIATYPE_INDEXTAB          7       /* Index Paper      */
#define CDM_MEDIATYPE_OTHERS            8       /* Other Paper      */
#define CDM_MEDIATYPE_BOND              9       /* Bond Paper       */
#define CDM_MEDIATYPE_THINNESS          10      /* Thin Paper       */
#define CDM_MEDIATYPE_LABELS            11      /* Label            */
#define CDM_MEDIATYPE_ENVELOPE          12      /* Envelope         */

/* CDM_ITEM_COLORMODE */
#define CDM_COLOR_AUTO                  0       /* Auto Detect */
#define CDM_COLOR_MONO                  1       /* Monochrome  */
#define CDM_COLOR_COLOR                 2       /* Full Color  */

/* CDM_ITEM_GRAPHICMODE */
#define CDM_MODE_AUTO                   0       /* Auto Detect */
#define CDM_MODE_PDL                    1       /* PDL Mode    */
#define CDM_MODE_RASTER                 2       /* Raster Mode */

/* CDM_ITEM_COLORDEPTH */
#define CDM_COLORDEPTH_PANEL            0       /* Printer Default */
#define CDM_COLORDEPTH_STD              1       /* Normal          */
#define CDM_COLORDEPTH_HQ1              2       /* High 1          */
#define CDM_COLORDEPTH_HQ2              3       /* High 2          */
#define CDM_COLORDEPTH_HQ               4       /* High            */

/* CDM_ITEM_COLORHALFTONE */
#define CDM_COLORHALFTONE_PRINTERDEFAULT    0       /* Printer Default */
#define CDM_COLORHALFTONE_GRADATION         1       /* Gradation       */
#define CDM_COLORHALFTONE_RESOLUTION        2       /* Resolution      */
#define CDM_COLORHALFTONE_GRADATION2        3       /* Color Tone      */
#define CDM_COLORHALFTONE_NONE              4       /* None            */
#define CDM_COLORHALFTONE_ED                5       /* Error Diffusion */
#define CDM_COLORHALFTONE_HIRESOLUTION      6       /* High Resolution */
#define CDM_COLORHALFTONE_PATTERN1          7       /* Pattern 1       */
#define CDM_COLORHALFTONE_PATTERN2          8       /* Pattern 2       */
#define CDM_COLORHALFTONE_PATTERN3          9       /* Pattern 3       */
#define CDM_COLORHALFTONE_PATTERN4          10      /* Pattern 4       */
#define CDM_COLORHALFTONE_PATTERN5          11      /* Pattern 5       */
#define CDM_COLORHALFTONE_PATTERN6          12      /* Pattern 6       */
#define CDM_COLORHALFTONE_STD               13      /* Standard        */


/* CDM_ITEM_MONOHALFTONE */
#define CDM_MONOHALFTONE_PRINTERDEFAULT     0       /* Printer Default */
#define CDM_MONOHALFTONE_GRADATION          1       /* Gradation       */
#define CDM_MONOHALFTONE_PATTERN1           2       /* Pattern 1       */
#define CDM_MONOHALFTONE_PATTERN2           3       /* Pattern 2       */
#define CDM_MONOHALFTONE_NONE               4       /* None            */
#define CDM_MONOHALFTONE_BLACK              5       /* None [Solid]    */
#define CDM_MONOHALFTONE_ED                 6       /* Error Diffusion */

/* CDM_ITEM_GLYPHINDEX */
#define CDM_GLYPH_OFF                   0       /* Enable Use Glyph Index Fonts  */
#define CDM_GLYPH_ON                    1       /* Disable Use Glyph Index Fonts */

/* CDM_ITEM_SAVETONER */
#define CDM_SAVETONER_OFF               0       /* Off             */
#define CDM_SAVETONER_ON                1       /* On              */
#define CDM_SAVETONER_PANEL             2       /* Printer Default */

/* CDM_ITEM_FINISHER */
#define CDM_FSH_NONE                    1       /* None                         */
#define CDM_FSH_MULTIOUTPUTTRAY_3       2       /* Multi-Tray 3                 */
#define CDM_FSH_FINISHER_C1             3       /* Finisher-C1                  */
#define CDM_FSH_SADDLEFINISHER_C2       4       /* Saddle Finisher-C2           */
#define CDM_FSH_FINISHER_E1             5       /* Finisher-E1                  */
#define CDM_FSH_FINISHER_D1             6       /* Finisher-D1                  */
#define CDM_FSH_FINISHER_PCL_D1_K1      6       /* Finisher-D1 or K1            */
#define CDM_FSH_SADDLEFINISHER_D2       7       /* Saddle Finisher-D2           */
#define CDM_FSH_7BINSORTER              8       /* 7-bin Sorter                 */
#define CDM_FSH_STAPLESTACKER           9       /* Stapler Stacker              */
#define CDM_FSH_FINISHER_G1             10      /* Finisher-G1                  */
#define CDM_FSH_FINISHER_F1             11      /* Finisher-F1                  */
#define CDM_FSH_SADDLEFINISHER_F2       12      /* Saddle Finisher-F2           */
#define CDM_FSH_SHIFTTRAY               13      /* Shift Tray                   */
#define CDM_FSH_MULTIOUTPUTTRAY_12      16      /* Multi-Tray 12                */
#define CDM_FSH_MULTITRAY_12            17      /* Multi-Tray 12 w/stapler (PCL only)      */
#define CDM_FSH_10BINSTAPLER            18      /* 10-bin Stapler Sorter (PCL only)        */
#define CDM_FSH_MULTITRAY_3             19      /* Multi-Tray 3 (PCL only)                 */
#define CDM_FSH_INNER2WAY               20      /* Inner 2way Tray              */
#define CDM_FSH_FINISHER_J1             21      /* Finisher-J1                  */
#define CDM_FSH_FINISHER_K2             22      /* Finisher-K2                  */
#define CDM_FSH_SADDLEFINISHER_K3       23      /* Saddle Finisher-K3           */
#define CDM_FSH_SADDLEFINISHER_K4       24      /* Saddle Finisher-K4 (PCL only) */
#define CDM_FSH_20BINSORTER             25      /* 20 Bin Sorter (PCL only)      */
#define CDM_FSH_STAPLERSORTER_C1        26      /* 20 Bin Stapler Sorter (PCL only)       */
#define CDM_FSH_MULTITRAY_7             27      /* Multi-Tray 7 (PCL only)                 */
#define CDM_FSH_FINISHER_K1             28      /* Finisher-K1                  */
#define CDM_FSH_SADDLEFINISHER_K3N      29      /* Saddle Finisher-K3N          */
#define CDM_FSH_FINISHER_L1             30      /* Finisher-L1                  */
#define CDM_FSH_INNER2WAY_B1            31      /* Inner 2way Tray-B1 (PCL only) */
#define CDM_FSH_18BINSTAPLER            32      /* 18-bin Stapler Sorter (Only PCL5c)       */
#define CDM_FSH_FINISHER_H1             33      /* Finisher-H1 (Only PCL5c)                 */
#define CDM_FSH_SADDLEFINISHER_H2       34      /* Saddle Finisher-H2 (Only PCL5c)          */
#define CDM_FSH_FINISHER_M1             35      /* Finisher-M1                  */
#define CDM_FSH_FINISHER_N1             36      /* Finisher-N1                  */
#define CDM_FSH_SADDLEFINISHER_N2       37      /* Saddle Finisher-N2           */

/* CDM_ITEM_OUTPUTPAPER */
#define CDM_OUTPUTPAPER_MATCHSIZE       0       /* Match Page Size */

/*---------------------------------------------------------*/
/* CDM_ITEM_BINDMARGIN_INCH */
#define CDM_MARGIN_INCH_OFF             0       /* Mm Appointment (PCL only)              */
#define CDM_MARGIN_INCH_ON              1       /* Inch Appointment (PCL only)            */

/* CDM_ITEM_INSERTION */
#define CDM_INSERTION_OFF               0       /* Disable Cover Insertion Unit (PCL only) */
#define CDM_INSERTION_ON                1       /* Enable Cover Insertion Unit (PCL only) */

/* CDM_ITEM_RENDERING */
#define CDM_RENDERING_1BPP              1       /* 1 BPP (PCL only)                       */
#define CDM_RENDERING_1BPPE             2       /* 1 BPP [Enhanced] (PCL only)            */
#define CDM_RENDERING_24BPP             3       /* 24 BPP (PCL only)                      */

/* CDM_ITEM_PRIORITY */
#define CDM_PRIORITY_QUICK              1       /* Priority:Quick (PCL only)              */
#define CDM_PRIORITY_FINE               2       /* Priority:Fine (PCL only)               */

/* CDM_ITEM_BANDING */
#define CDM_BANDING_AUTO                1       /* Banding:Auto (PCL only)                */
#define CDM_BANDING_ON                  2       /* Banding:On (PCL only)                  */

/* CDM_ITEM_TEXTGRAPHICS */
#define CDM_TEXTGRAPHICS_OFF            0       /* Disable Print Text as Graphics (PCL only) */
#define CDM_TEXTGRAPHICS_ON             1       /* Enable Print Text as Graphics (PCL only) */

/* CDM_ITEM_USEPRTFONT */
#define CDM_USEPRTFONT_OFF              0       /* Disable Use Printer Fonts (PCL only)   */
#define CDM_USEPRTFONT_ON               1       /* Enable Use Printer Fonts (PCL only)    */

/* CDM_ITEM_TTMODE */
#define CDM_TTOMDE_TT                   1       /* Download as TrueType (PCL only)        */
#define CDM_TTOMDE_IMAGE                2       /* Download as Bit Image (PCL only)       */

/* CDM_ITEM_REFINE */
#define CDM_REFINE_OFF                  0       /* Disable Image Refinement (PCL only)    */
#define CDM_REFINE_ON                   1       /* Enable Image Refinement (PCL only)      */
#define CDM_REFINE_PRT                  2       /* Printer Default (PCL only)             */

/* CDM_ITEM_MULTIPAGE */
#define CDM_MULTIPAGE_OFF               0       /* Disable Multi-Page (PCL only)          */
#define CDM_MULTIPAGE_ON                1       /* Enable Multi-Page (PCL only)           */

/* CDM_ITEM_LINEREFINE */
#define CDM_LINEREFINE_OFF              0       /* Disable Line Refinement (PCL only)     */
#define CDM_LINEREFINE_ON               1       /* Enable Line Refinement (PCL only)      */
#define CDM_LINEREFINE_PRT              2       /* Printer Default (PCL only)             */

/* CDM_ITEM_EMF */
#define CDM_EMF_OFF                     0       /* Disable EMF Spooling (PCL only)        */
#define CDM_EMF_ON                      1       /* Enable EMF Spooling (PCL only)         */

/* CDM_ITEM_REVPRINT */
#define CDM_REVPRINT_OFF                0       /* Disable Reverse Output Order (PCL only) */
#define CDM_REVPRINT_ON                 1       /* Enable Reverse Output Order (PCL only) */

/* CDM_ITEM_EDGING */
#define CDM_EDGING_NONE                 0       /* None (PCL only)                        */
#define CDM_EDGING_SOLID                1       /* Solid Line (PCL only)                 */
#define CDM_EDGING_DASH                 2       /* Dashed Line (PCL only)                 */
#define CDM_EDGING_DOT                  3       /* Dotted Line (PCL only)                */
#define CDM_EDGING_CHAIN                4       /* Chain Line (PCL only)                  */
#define CDM_EDGING_CHAINDD              5       /* Chain Double-dashed Line (PCL only)    */
#define CDM_EDGING_LINE3                6       /* 3-dimensional Line (PCL only)          */
#define CDM_EDGING_LINE                 7       /* Transparent (PCL only)                 */
#define CDM_EDGING_DSOLID               8       /* Double Solid Line (PCL only)           */
#define CDM_EDGING_CROP                 9       /* Crop Marks (PCL only)                  */
#define CDM_EDGING_CORNER               10      /* Corner Marks (PCL only)                */

/* CDM_ITEM_PRINTDATE */
#define CDM_PRINTDATE_NONE              0       /* None (PCL only)                        */
#define CDM_PRINTDATE_UPLEFT            1       /* Upper Left (PCL only)                  */
#define CDM_PRINTDATE_UPMIDDLE          2       /* Upper Middle (PCL only)                */
#define CDM_PRINTDATE_UPRIGHT           3       /* Upper Right (PCL only)                 */
#define CDM_PRINTDATE_LWLEFT            4       /* Lower Left (PCL only)                  */
#define CDM_PRINTDATE_LWMIDDLE          5       /* Lower Middle (PCL only)                */
#define CDM_PRINTDATE_LWRIGHT           6       /* Lower Right (PCL only)                 */

/* CDM_ITEM_PRINTUSER */
#define CDM_PRINTUSER_NONE              0       /* None (PCL only)                        */
#define CDM_PRINTUSER_UPLEFT            1       /* Upper Left (PCL only)                  */
#define CDM_PRINTUSER_UPMIDDLE          2       /* Upper Middle (PCL only)                */
#define CDM_PRINTUSER_UPRIGHT           3       /* Upper Right (PCL only)                 */
#define CDM_PRINTUSER_LWLEFT            4       /* Lower Left (PCL only)                  */
#define CDM_PRINTUSER_LWMIDDLE          5       /* Lower Middle (PCL only)                */
#define CDM_PRINTUSER_LWRIGHT           6       /* Lower Right (PCL only)                 */

/* CDM_ITEM_PRINTPAGENO */
#define CDM_PRINTPAGENO_NONE            0       /* None (PCL only)                        */
#define CDM_PRINTPAGENO_UPLEFT          1       /* Upper Left (PCL only)                  */
#define CDM_PRINTPAGENO_UPMIDDLE        2       /* Upper Middle (PCL only)                */
#define CDM_PRINTPAGENO_UPRIGHT         3       /* Upper Right (PCL only)                 */
#define CDM_PRINTPAGENO_LWLEFT          4       /* Lower Left (PCL only)                  */
#define CDM_PRINTPAGENO_LWMIDDLE        5       /* Lower Middle (PCL only)                */
#define CDM_PRINTPAGENO_LWRIGHT         6       /* Lower Right (PCL only)                 */

/* CDM_ITEM_PRINTQUALITY */
#define CDM_PRINTQUALITY_DEFAULT	0	/* Standard (GARO only) */
#define CDM_PRINTQUALITY_FINE		1	/* Fine (GARO only) */
#define CDM_PRINTQUALITY_QUICK		2	/* Quick (GARO only) */
#define CDM_PRINTQUALITY_CUSTOM		3	/* Custom (GARO only) */

/* CDM_ITEM_PAPERCUT */
#define CDM_PAPERCUT_AUTO		0	/* Auto (GARO only) */
#define CDM_PAPERCUT_NOCUT		1	/* No cut (GARO only) */
#define CDM_PAPERCUT_CUTLINE	2	/* Cut line (GARO only) */


/*---------------------------------------------------------*/
/* CDM_ITEM_FAX_COVERPAGETEMPLATE */
#define CDM_FAX_TEMPLATE_MAX            31
          /* maximum size of template name */
typedef struct tagCDMFaxCPTemplate {
    char        szTemplate[ CDM_FAX_TEMPLATE_MAX + 1 ]; /* template name  */
} CDM_FAX_CPTEMPLATE, FAR *LPCDM_FAX_CPTEMPLATE;

/* CDM_ITEM_FAX_DESCRIPTIONMASK */      /* fax ui flags */
#define CDM_FAX_DESCRIBE_PREFIXNUMBER   0x00004000  /* enable prefix number */
#define CDM_FAX_EACHRECIPIENT_OFF       0x00008000
          /* "different sheet to each recipient" function is ineffective */

/* CDM_ITEM_FAX_BROADCASTSPEC */        /* structure of broadcasting capability */
typedef struct _tagCDM_FaxBroadcastSpec {
    short       nDevBroadcastSpec;
          /* maximum broadcast capability per fax job for each device */
    short       nMaxBroadcastNone;
          /* maximum broadcast capability of "none" */
    short       nMaxBroadcastAll;
          /* maximum broadcast capability of "same to all" */
    short       nMaxBroadcastEach;
          /* maximum broadcast capability of "different to each" */
} CDM_FAX_BROADCASTSPEC, FAR *LPCDM_FAX_BROADCASTSPEC;

/* CDM_ITEM_FAX_PREFIXNUMBER */
#define CDM_FAX_PREFIX_MAX              5
          /* maximum size of prefix number */

/* CDM_ITEM_FAX_TRANSMODE */            /* transmitting resolution */
#define CDM_FAX_TRANSMODE_FINE          0   /* fine       */
#define CDM_FAX_TRANSMODE_ULTRAFINE     1   /* ultra fine */

/* CDM_ITEM_FAX_UIOPEN */               /* special UI */
#define CDM_FAX_SPECIALUI_OPEN          0   /* UI open     */
#define CDM_FAX_SPECIALUI_CLOSE         1   /* not UI open */

/* CDM_ITEM_FAX_REGISTTEMPLATE */
#define CDM_FAX_TEMPLATEPATH_MAX        255
          /* maximum size of template file path */

/* cover sheet attachment */
#define CDM_FAX_COVERPAGE_OFF           0
          /* none */
#define CDM_FAX_COVERPAGE_ALL           1
          /* same sheet to all recipient */
#define CDM_FAX_COVERPAGE_EACH          2
          /* different sheet to each recipient */

/* maximum size of fax transmitting data */
#define CDM_FAX_SENDER_MAX              85  /* name       */
#define CDM_FAX_NUMBER_MAX              45  /* fax number */
#define CDM_FAX_COMPANY_MAX             40  /* company    */
#define CDM_FAX_DEPARTMENT_MAX          40  /* department */
#define CDM_FAX_NOTICE_MAX              30  /* notice     */
#define CDM_FAX_NOTE_MAX                255 /* comment    */

/* cover sheet description flags */
#define CDM_FAX_DESCRIBE_SENDERNAME             0x00000001
          /* sender name */
#define CDM_FAX_DESCRIBE_SENDERNUMBER           0x00000002
          /* sender fax number */
#define CDM_FAX_DESCRIBE_SENDERCOMPANY          0x00000004
          /* sender company */
#define CDM_FAX_DESCRIBE_SENDERDEPARTMENT       0x00000008
          /* sender department */
#define CDM_FAX_DESCRIBE_RECIPIENTNAME          0x00000010
          /* recipient name */
#define CDM_FAX_DESCRIBE_RECIPIENTNUMBER        0x00000020
          /* recipient fax number */
#define CDM_FAX_DESCRIBE_RECIPIENTCOMPANY       0x00000040
          /* recipient company */
#define CDM_FAX_DESCRIBE_RECIPIENTDEPARTMENT    0x00000080
          /* recipient department */
#define CDM_FAX_DESCRIBE_NOTICE                 0x00000100
          /* notice */
/*---------------------------------------------------------*/

/* the STAMPSTRINGS structure */
typedef struct tagSTAMPSTRINGSA{
    char    stpTitle[64];               /* The name of a watermark */
    char    stpText[64];                /* The text of a watermark */
} STAMPSTRINGSA, NEAR *PSTAMPSTRINGSA, FAR *LPSTAMPSTRINGSA;
#if defined(_WIN32)
typedef struct tagSTAMPSTRINGSW{
    WCHAR   stpTitle[32];               /* The name of a watermark */
    WCHAR   stpText[32];                /* The text of a watermark */
} STAMPSTRINGSW, NEAR *PSTAMPSTRINGSW, FAR *LPSTAMPSTRINGSW;
#endif

#ifdef UNICODE
	typedef STAMPSTRINGSW       STAMPSTRINGS;
	typedef PSTAMPSTRINGSW      PSTAMPSTRINGS;
	typedef LPSTAMPSTRINGSW     LPSTAMPSTRINGS;
#else
	typedef STAMPSTRINGSA       STAMPSTRINGS;
	typedef PSTAMPSTRINGSA      PSTAMPSTRINGS;
	typedef LPSTAMPSTRINGSA     LPSTAMPSTRINGS;
#endif

/* the CDM_PAPERSELECTION structure */
typedef struct tagCDM_PAPERSELECTION{
    DWORD   dwPSType;              /* page selection method      */
    WORD    wPSFirstPage;          /* first or cover page        */
    WORD    wPS2ndPage;            /* second page                */
    WORD    wPSOthers;             /* other pages (entire pages) */
    WORD    wPSLastPage;           /* last page                  */
    WORD    wPSInterLeaf;          /* transparency interleaving  */
} CDM_PAPERSELECTION, NEAR *PCDM_PAPERSELECTION, FAR *LPCDM_PAPERSELECTION;



/*===============================================================*
 * Function Prototype                                            *
 *===============================================================*/

#if defined(_WIN32)

#ifdef UNICODE
#define GetCDMInitFunction( hDrv ) (CANONDEVICEMODEINIT)GetProcAddress( (hDrv), "CanonDeviceModeInitW" )
typedef HANDLE (WINAPI *CANONDEVICEMODEINIT)(
                     HWND      hParentWnd,
                     LPCWSTR   pszDevName,
                     LPCWSTR   pszDevPort,
                     LPDEVMODE lpDevMode );
#else
#define GetCDMInitFunction( hDrv ) (CANONDEVICEMODEINIT)GetProcAddress( (hDrv), "CanonDeviceModeInitA" )
typedef HANDLE (WINAPI *CANONDEVICEMODEINIT)(
                     HWND      hParentWnd,
                     LPCSTR    pszDevName,
                     LPCSTR    pszDevPort,
                     LPDEVMODE lpDevMode );
#endif

typedef DWORD (WINAPI *CANONDEVICEMODEEX)(
                     HANDLE hCDM,
                     DWORD     FuncID,
                     DWORD     ItemID,
                     LPDEVMODE lpDevMode,
                     LPVOID    pItem );
#ifdef UNICODE
#define GetCDMExFunction( hDrv ) (CANONDEVICEMODEEX)GetProcAddress( (hDrv), "CanonDeviceModeExW" )
#else
#define GetCDMExFunction( hDrv ) (CANONDEVICEMODEEX)GetProcAddress( (hDrv), "CanonDeviceModeExA" )
#endif

typedef HANDLE (WINAPI *CANONDEVICEMODETERM)( HANDLE hCDM );
#define GetCDMTermFunction( hDrv ) (CANONDEVICEMODETERM)GetProcAddress( (hDrv), "CanonDeviceModeTerm" )

#endif  /* _WIN32 */



/*===============================================================*
 * Form ESCAPE                                                   *
 *===============================================================*/

#define CESCFIRST32             8448
#define GETSETJOBMODE32         (CESCFIRST32+6)
#define GETSETOVERLAYMODE32     (CESCFIRST32+7)
#define GETSETNEWFORMENTRY32    (CESCFIRST32+8)
#define GETSETFORMENTRY32       (CESCFIRST32+9)

typedef struct tagFORMFILE{
    short   ffOverlayPageNo;
          /* The number of the overlay page */
    char    ffFormFileName[128];
          /* The filename of a form file    */
}FORMFILE, NEAR *PFORMFILE, FAR *LPFORMFILE;

#endif  /* INC_CNPDSDK */

/* End of cnpdsdk.h file */
