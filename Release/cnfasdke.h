/******************************************************************************
*                                                                             *
* cnfasdke.h - Canon AddressBookDataManager SDK Version 1.11                  *
*                                                                             *
* Copyright (C) 2001 Canon Inc.  All rights reserved.                         *
*                                                                             *
******************************************************************************/

#ifndef __CNFASDKE_H__
#define __CNFASDKE_H__

#ifdef __cplusplus
extern "C" {
#endif // __cplusplus

#pragma pack(4)

/*------------------------------------------------------------------------------*/
/*						RECORD KEYS												*/
/*------------------------------------------------------------------------------*/
#define CARECORD_DISPLAY_NAME				0x0001		/* recipient name	*/
#define CARECORD_NAME_PREFIX				0x0002		/* prefix		*/
#define	CARECORD_NAME_FIRST					0x0003		/* first name	*/
#define	CARECORD_NAME_MIDDLE				0x0004		/* middle name 	*/
#define CARECORD_NAME_LAST					0x0005		/* last name 	*/
#define	CARECORD_NAME_SUFFIX				0x0006		/* suffix 		*/
#define	CARECORD_NOTE						0x0008		/* notes		*/
#define	CARECORD_COMPANY_NAME				0x0200		/* company name */
#define	CARECORD_DEPARTMENT_NAME			0x0201		/* dept./div. 	*/
#define	CARECORD_HOME_FAX_NUMBER			0x0110		/* secondary fax number					*/
#define CARECORD_HOME_NUMBER_DESCRIPTION	0x0111		/* secondary fax number's description		*/
#define	CARECORD_HOME_NUMBER_FCODE			0x0112		/* secondary fax number's subaddress 		*/
#define CARECORD_HOME_NUMBER_PASSWORD		0x0113		/* secondary fax number's password		*/
#define	CARECORD_OFFICE_FAX_NUMBER			0x0210		/* primary fax number					*/
#define CARECORD_OFFICE_NUMBER_DESCRIPTION	0x0211		/* primary fax number's description 	*/
#define	CARECORD_OFFICE_NUMBER_FCODE		0x0212		/* primary fax number's subaddress	*/
#define CARECORD_OFFICE_NUMBER_PASSWORD		0x0213		/* primary fax number's password		*/
#define CARECORD_GROUP_ID					0x0030		/* group member		*/

/*------------------------------------------------------------------------------*/
/*						Other define											*/
/*------------------------------------------------------------------------------*/
/*for RecordID Mask*/
#define	CARECORD_GROUPID_MASK				0x80000000	/* recordID's mask	*/

/* ulFaxNumberMask in CAGROUPPROP structure */
#define CAFAXNUMBER_OFFICE					0x0001		/* primary fax number 							*/
#define CAFAXNUMBER_HOME					0x0002		/* secondary fax number							*/
#define CAFAXNUMBER_BOTH					0x0003		/* primary fax number and secondary fax number	*/

/*for caGetRecordIdA()	nFlag */
#define CARECORDID_CONTACT					0x0001		/* person records */
#define CARECORDID_GROUP					0x0002		/* group records	*/
#define CARECORDID_ALL						0x0003		/* all records		*/

/* string limit */
#define	CALIMIT_PREFIX		(16)				/* prefix					*/
#define	CALIMIT_FIRST		(20)				/* first name				*/
#define	CALIMIT_MIDDLE		(20)				/* middle name 				*/
#define	CALIMIT_LAST		(30)				/* last name 				*/
#define	CALIMIT_SUFFIX		(16)				/* suffix 					*/
#define	CALIMIT_COMPANY		(40)				/* company name 			*/
#define	CALIMIT_DEPTDIV		(40)				/* dept./div. 				*/
#define	CALIMIT_DISPNAME	(85)				/* recipient name			*/
#define	CALIMIT_FAXNUM		(45)				/* fax number				*/
#define	CALIMIT_DESCRIPT	(30)				/* fax number description	*/
#define CALIMIT_GROUPNAME	(48)				/* group name				*/
#define CALIMIT_FCODE		(20)				/* fax number subaddress	*/
#define CALIMIT_PASSWORD	(20)				/* fax number password		*/
#define CALIMIT_GROUPNOTE	(32)				/* group notes				*/
#define CALIMIT_NOTE		(255)				/* person notes				*/

#define	CALIMIT_FAXNUM_EXCEPT_HYPHEN	(38)	/* maximum byte of the fax number except the hyphen  */

/* MAX */
#define CAMAX_CONTACTENTRY	3000				/* maximum person records		 */
#define CAMAX_GROUPENTRY	3000				/* maximum group records		 */
#define	CAMAX_GROUPMEMBER	256					/* maximum member in one group	 */

/*------------------------------------------------------------------------------*/
/*						structure												*/
/*------------------------------------------------------------------------------*/

/* The item registered into a person and a group record.*/
typedef struct _tagCaProp
{
	DWORD nRecordKey;					/* record key (CARECORD_?????)	*/
	char  tcString[256];				/* string						*/
} CAPROP, FAR *LPCAPROP;

/* The structure of the record ID registered into a group record*/
typedef struct _tagCaGroupProp
{
	DWORD nRecordKey;					/* record key ( only CARECORD_GROUP_ID)	*/
	DWORD ulRecordId;					/* record ID 							*/
	DWORD ulFaxNumberMask;				/* fax number's mask(CAFAXNUMBER_????)	*/
	DWORD ulAlign[32];					/* reserve:no used 						*/
} CAGROUPPROP, FAR *LPCAGROUPPROP;

/* table of CAPROP(GROUPPROP) structure*/
typedef struct _tagCaRecordProp
{
	DWORD cEntry;						/* The number of elements of table 					*/
	LPVOID lpProp;						/* pointer to table of CAPROP(GROUPPROP) structure	*/
} CARECORDPROP, FAR *LPCARECORDPROP;

/* table of record ID*/
typedef struct _tagCaRecordId
{
	DWORD ulEntry;						/* The number of elements of table 	*/
	LPDWORD lpulRecordId;				/* pointer to table of record ID	*/
} CARECORDID, FAR *LPCARECORDID;

/*------------------------------------------------------------------------------*/
/*						errcode													*/
/*------------------------------------------------------------------------------*/

/* from AddressBook Manager */
#define CAERROR_MAGICNUMBER_MISMATCHED	0x0001		/* The address book is the format which cannot be recognized.	*/
#define CAERROR_READ_FAILED				0x0002		/* read failed					*/
#define CAERROR_WRITE_FAILED			0x0003		/* write failed					*/
#define CAERROR_RECORD_NOT_FOUND		0x0004		/* record not found 			*/
#define CAERROR_RECORD_DELETED			0x0005		/* record is deleted 			*/
#define	CAERROR_RECORDID_EXHAUST		0x0006		/* over maximum record ID value	*/

/* from Addressbook Data Maneger */
#define	CAERROR_NOT_ACCESS_ABMAN		0x0011		/* not access AddressBook Manager's dll			*/
#define	CAERROR_INVALID_PARAM			0x0012		/* palameter error 								*/
#define CAERROR_NOMEMORY     			0x0013		/* no memory									*/
#define	CAERROR_UNKNOWN					0x0014		/* other error									*/
#define CAERROR_OVER_MAXENTRY			0x0015		/* over maximum record 							*/
#define	CAERROR_ALREADY_OPEN			0x0016		/* someone already opens the AddressBook file	*/

/*------------------------------------------------------------------------------*/
/*						prototypes												*/
/*------------------------------------------------------------------------------*/

/*************************************************************************
	1)caOpenAddressBookA()		initializes address book 				
	2)caCloseAddressBookA()		terminates address book processing		
	3)caAddContactEntryA()		creates person record					
	4)caAddGroupEntryA()		creates group record					
	5)caGetPropsA()				gets record information				 	
	6)caGetRecordIdA()			gets record id							
	7)caSetPropsA()				sets record information				 	
	8)caDeletePropsA()			deletes a record						
	9)caInitADMA()				initializes AddressBook Data Manager	
	10)caEndADMA()				closes AddressBook Data Manager			
 *************************************************************************/

/* prototype declaration	*/
HANDLE WINAPI caOpenAddressBookA(char FAR* lpDBFile, LPDWORD lpdwError);
void WINAPI caCloseAddressBookA(HANDLE hDB);
DWORD WINAPI caAddContactEntryA(HANDLE hDB, LPDWORD lpdwError, LPCARECORDPROP lpRecord);
DWORD WINAPI caAddGroupEntryA(HANDLE hDB, LPDWORD lpdwError, LPCARECORDPROP lpRecord);
long WINAPI caGetPropsA(HANDLE hDB, DWORD ulRecordId, LPDWORD lpdwError, LPCARECORDPROP lpRecord);
long WINAPI caGetRecordIdA(HANDLE hDB, DWORD nFlag, LPDWORD lpdwError, LPCARECORDID lpRecordId);
BOOL WINAPI caSetPropsA(HANDLE hDB, DWORD ulRecordId, LPDWORD lpdwError, LPCARECORDPROP lpRecord);
BOOL WINAPI caDeletePropsA(HANDLE hDB, BOOL bCompact, LPDWORD lpdwError, DWORD ulRecordId);
BOOL WINAPI caInitADMA(LPDWORD lpdwError);
void WINAPI caEndADMA();

/* GetProcAddress() Macro	*/
#define GetADMOpenFunction( hDrv )			(LPFNCAOPENADDRESSBOOK)GetProcAddress( (hDrv), "caOpenAddressBookA" )
#define GetADMAddCEntryFunction( hDrv )	(LPFNCAADDCONTACTENTRY)GetProcAddress( (hDrv), "caAddContactEntryA" )
#define GetADMAddGEntryFunction( hDrv )	(LPFNCAADDGROUPENTRY)GetProcAddress( (hDrv), "caAddGroupEntryA" )
#define GetADMGetRecordIdFunction( hDrv )	(LPFNCAGETRECORDID)GetProcAddress( (hDrv), "caGetRecordIdA" )
#define GetADMGetPropsFunction( hDrv )		(LPFNCAGETPROPS)GetProcAddress( (hDrv), "caGetPropsA" )
#define GetADMSetPropsFunction( hDrv )		(LPFNCASETPROPS)GetProcAddress( (hDrv), "caSetPropsA" )
#define GetADMDeletePropsFunction( hDrv )	(LPFNCADELETEPROPS)GetProcAddress( (hDrv), "caDeletePropsA" )
#define GetADMCloseFunction( hDrv )			(LPFNCACLOSEADDRESSBOOK)GetProcAddress( (hDrv), "caCloseAddressBookA" )
#define GetADMInitFunction( hDrv )			(LPFNCAINITADM)GetProcAddress( (hDrv), "caInitADMA" )
#define GetADMEndFunction( hDrv )			(LPFNCAENDADM)GetProcAddress( (hDrv), "caEndADMA" )

/* typedef declaration	*/
typedef HANDLE	(WINAPI* LPFNCAOPENADDRESSBOOK)(char FAR*, LPDWORD);
typedef void 	(WINAPI* LPFNCACLOSEADDRESSBOOK)(HANDLE);
typedef DWORD 	(WINAPI* LPFNCAADDCONTACTENTRY)(HANDLE, LPDWORD, LPCARECORDPROP);
typedef DWORD 	(WINAPI* LPFNCAADDGROUPENTRY)(HANDLE, LPDWORD, LPCARECORDPROP);
typedef long 	(WINAPI* LPFNCAGETPROPS)(HANDLE, DWORD, LPDWORD, LPCARECORDPROP);
typedef long 	(WINAPI* LPFNCAGETRECORDID)(HANDLE, DWORD, LPDWORD, LPCARECORDID);
typedef BOOL 	(WINAPI* LPFNCASETPROPS)(HANDLE, DWORD, LPDWORD, LPCARECORDPROP);
typedef BOOL 	(WINAPI* LPFNCADELETEPROPS)(HANDLE, BOOL, LPDWORD, DWORD);
typedef BOOL 	(WINAPI* LPFNCAINITADM)(LPDWORD);
typedef void 	(WINAPI* LPFNCAENDADM)(VOID);

#pragma pack()

#ifdef __cplusplus
}
#endif // __cplusplus
#endif // !__CNFASDKE_H__
