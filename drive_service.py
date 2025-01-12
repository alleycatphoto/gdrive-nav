from google.oauth2 import service_account
from googleapiclient.discovery import build
from googleapiclient.http import MediaIoBaseDownload
import io
import logging

logger = logging.getLogger(__name__)

class DriveService:
    def __init__(self, credentials_file):
        self.credentials = service_account.Credentials.from_service_account_file(
            credentials_file,
            scopes=['https://www.googleapis.com/auth/drive.readonly']
        )
        self.service = build('drive', 'v3', credentials=self.credentials)
        
    def list_files(self, folder_id='root'):
        try:
            results = self.service.files().list(
                q=f"'{folder_id}' in parents and trashed=false",
                pageSize=1000,
                fields="files(id, name, mimeType, thumbnailLink, webViewLink)",
                orderBy="folder,name"
            ).execute()
            
            files = results.get('files', [])
            return [{
                'id': f['id'],
                'name': f['name'],
                'mimeType': f['mimeType'],
                'thumbnailLink': f.get('thumbnailLink'),
                'webViewLink': f.get('webViewLink'),
                'isFolder': f['mimeType'] == 'application/vnd.google-apps.folder'
            } for f in files]
        except Exception as e:
            logger.error(f"Error listing files: {str(e)}")
            raise

    def get_breadcrumbs(self, folder_id):
        if folder_id == 'root':
            return [{'id': 'root', 'name': 'Root'}]
            
        breadcrumbs = []
        current_id = folder_id
        
        while current_id != 'root':
            try:
                folder = self.service.files().get(
                    fileId=current_id,
                    fields='id,name,parents'
                ).execute()
                
                breadcrumbs.insert(0, {
                    'id': folder['id'],
                    'name': folder['name']
                })
                
                if 'parents' in folder:
                    current_id = folder['parents'][0]
                else:
                    break
            except Exception as e:
                logger.error(f"Error getting breadcrumbs: {str(e)}")
                break
                
        breadcrumbs.insert(0, {'id': 'root', 'name': 'Root'})
        return breadcrumbs

    def download_file(self, file_id):
        try:
            request = self.service.files().get_media(fileId=file_id)
            fh = io.BytesIO()
            downloader = MediaIoBaseDownload(fh, request)
            done = False
            
            while done is False:
                status, done = downloader.next_chunk()
                
            return fh.getvalue()
        except Exception as e:
            logger.error(f"Error downloading file: {str(e)}")
            raise

    def get_thumbnail(self, file_id):
        try:
            file = self.service.files().get(
                fileId=file_id,
                fields='thumbnailLink'
            ).execute()
            
            if 'thumbnailLink' in file:
                return self.download_file(file_id)
            return None
        except Exception as e:
            logger.error(f"Error getting thumbnail: {str(e)}")
            raise

    def get_preview_link(self, file_id):
        try:
            file = self.service.files().get(
                fileId=file_id,
                fields='webViewLink'
            ).execute()
            return file.get('webViewLink')
        except Exception as e:
            logger.error(f"Error getting preview link: {str(e)}")
            raise
