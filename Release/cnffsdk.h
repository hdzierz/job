/*******************************************************************************
;
; cnffsdk.h -  Canon FaxDataFile APIs Ver1.11
;
; Description: Header File for FaxDataFile APIs 
;  			   - Wrapper Functions for FaxDataManager APIs
;
;  Copyright(C) 2001 Canon Inc.  All Rights Reserved.
;
*******************************************************************************/

#ifndef __CNFFSDK_H__
#define __CNFFSDK_H__

#ifdef	__cplusplus
extern "C" {
#endif


/*------------------------------------------------------------------------------*/
/*							Definitions											*/
/*------------------------------------------------------------------------------*/
#define	CF_INVALID_SEEDVALUE	0						/* Initial Value of faxFileNameSeed */

/* Buffer Length */
#define	CF_MAX_NAME					88					/* Name              */
#define	CF_MAX_NUMBER				48					/* FAX Number        */
#define CF_MAX_COMPANY				44					/* Company Name      */
#define	CF_MAX_DEPARTMENT			44					/* Dept./Div. Name   */
#define	CF_MAX_NOTICE				32					/* Notice            */
#define	CF_MAX_COMMENT				256					/* Comment           */
#define	CF_MAX_PREFIX				8					/* Prefix FAX Number */
#define CF_MAX_TEMPLATE				32					/* Template Name     */


/* Error Code	*/
#define CFERROR_SUCCESS				0x00000000			/* Successful Fin                                        */
#define	CFERROR_CANNOT_LOADMODULE	0x80000001			/* can't load cnxdman.dll                                */
#define	CFERROR_INVALID_VERSION		0x80000002			/* version missmatch between cnffsdk.dll and cnxdman.dll */
#define	CFERROR_INVALID_PARAMETER	0x80000003			/* invalid parameter                                     */
#define	CFERROR_INVALID_SEED		0x80000004			/* invalid seed value                                    */
#define	CFERROR_INVALID_STATE		0x80000005			/* FAX Job status error                                  */
#define CFERROR_POORMEMORY			0x80000006			/* no memory                                             */



/*------------------------------------------------------------------------------*/
/*							Structures											*/
/*------------------------------------------------------------------------------*/

/* Information of the Fax Job	*/
typedef struct tagCF_FaxJobInfo {
	short		nSize;									/* Structure Size                                           */
	DWORD		dwFlags;								/* Job Information Flags                                    */
	short		nTransMode;								/* Transfer Resolution Mode                                 */
	short		nCoverPageMode;							/* CoverSheet Style                                         */
	short		nBroadcastCap;							/* Maximum broadcast capability per fax job for each device */
	short		nTotalAddr;								/* Total Recipient Count of this FAX Job                    */
	char		szPrefix[ CF_MAX_PREFIX ];				/* Prefix Fax Number string                                 */
} CF_FAXJOBINFO, *PCF_FAXJOBINFO, FAR *LPCF_FAXJOBINFO;


/* Information of the Recipients */
typedef struct tagCF_FaxRecipient {
	short		nSize;									/* Structure Size            */
	char		szName[ CF_MAX_NAME ];					/* Recipient Name            */
	char		szNumber[ CF_MAX_NUMBER ];				/* Recipient Fax Number      */
	char		szCompany[ CF_MAX_COMPANY ];			/* Recipient Company Name    */
	char		szDepartment[ CF_MAX_DEPARTMENT ];		/* Recipient Dept./Div. Name */
} CF_FAXRECIPIENT, *PCF_FAXRECIPIENT, FAR *LPCF_FAXRECIPIENT;


/* Information of the CoverSheet */
typedef struct tagCF_FaxCoverPageInfo {
	short		nSize;									/* Structure Size               */
	DWORD		nDescriptionMask;						/* CoverSheet Description Flags */
	char		szTxName[ CF_MAX_NAME ];				/* Sender Name                  */
	char		szTxNumber[ CF_MAX_NUMBER ];			/* Sender Fax Number            */
	char		szTxCompany[ CF_MAX_COMPANY ];			/* Sender Company Name          */
	char		szTxDepartment[ CF_MAX_DEPARTMENT ];	/* Sender Dept./Div. Name       */
	char		szNotice[ CF_MAX_NOTICE ];				/* Notice string on CoverSheet  */
	char		szComment[ CF_MAX_COMMENT ];			/* Comment string on CoverSheet */
	char		szTemplate[ CF_MAX_TEMPLATE ];			/* CoverSheet Template Name     */
} CF_FAXCPINFO, *PCF_FAXCPINFO, FAR *LPCF_FAXCPINFO;



/*------------------------------------------------------------------------------*/
/*						Function Prototype										*/
/*------------------------------------------------------------------------------*/
BOOL WINAPI cfSetFaxDataFileA( HWND, LPTSTR, DWORD, LPCF_FAXJOBINFO, LPCF_FAXRECIPIENT, LPCF_FAXCPINFO, LPDWORD );
BOOL WINAPI cfDeleteFaxDataFileA( DWORD, LPDWORD );

#define	GetFDFSetFunction( hLib )		(LPFNCFSETFAXDATAFILE)GetProcAddress( (hLib), "cfSetFaxDataFileA" )
#define	GetFDFDeleteFunction( hLib )	(LPFNCFDELETEFAXDATAFILE)GetProcAddress( (hLib), "cfDeleteFaxDataFileA" )

typedef	BOOL (WINAPI *LPFNCFSETFAXDATAFILE)( HWND, LPTSTR, DWORD, LPCF_FAXJOBINFO, LPCF_FAXRECIPIENT, LPCF_FAXCPINFO, LPDWORD );
typedef BOOL (WINAPI *LPFNCFDELETEFAXDATAFILE)( DWORD, LPDWORD );

#ifdef	__cplusplus
}
#endif

#endif	/* __CNFFSDK_H__ */
