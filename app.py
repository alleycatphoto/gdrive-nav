import os
import logging
from flask import Flask, render_template, request, jsonify, send_file
from drive_service import DriveService
import io
import traceback

logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

app = Flask(__name__)
app.secret_key = os.urandom(24)

CREDENTIALS_FILE = 'attached_assets/dna-distribution-portal-444605-8de102eaeb67.json'
DEFAULT_ROOT_FOLDER = '1EWkdMmyFUdX7rui-ZzaP6lHWfM10SNAk'
drive_service = DriveService(CREDENTIALS_FILE)

@app.route('/')
def index():
    folder_id = request.args.get('folder_id', DEFAULT_ROOT_FOLDER)
    return render_template('browser.html', folder_id=folder_id)

@app.route('/list')
def list_files():
    try:
        folder_id = request.args.get('folder_id', DEFAULT_ROOT_FOLDER)
        logger.debug(f"Listing files for folder: {folder_id}")

        files = drive_service.list_files(folder_id)
        breadcrumbs = drive_service.get_breadcrumbs(folder_id)

        response_data = {
            'files': files,
            'breadcrumbs': breadcrumbs,
            'debug_info': {
                'folder_id': folder_id,
                'files_count': len(files),
                'breadcrumbs_count': len(breadcrumbs)
            }
        }
        logger.debug(f"API Response: {response_data}")
        return jsonify(response_data)
    except Exception as e:
        error_details = {
            'error': str(e),
            'traceback': traceback.format_exc(),
            'folder_id': request.args.get('folder_id', DEFAULT_ROOT_FOLDER)
        }
        logger.error(f"Error listing files: {error_details}")
        return jsonify(error_details), 500

@app.route('/download')
def download_file():
    try:
        file_id = request.args.get('file_id')
        file_name = request.args.get('file_name')

        if not file_id or not file_name:
            return jsonify({'error': 'Missing file_id or file_name'}), 400

        logger.debug(f"Downloading file: {file_id} ({file_name})")
        content = drive_service.download_file(file_id)
        return send_file(
            io.BytesIO(content),
            download_name=file_name,
            as_attachment=True
        )
    except Exception as e:
        error_details = {
            'error': str(e),
            'traceback': traceback.format_exc(),
            'file_id': request.args.get('file_id'),
            'file_name': request.args.get('file_name')
        }
        logger.error(f"Error downloading file: {error_details}")
        return jsonify(error_details), 500

@app.route('/thumbnail/<file_id>')
def get_thumbnail(file_id):
    try:
        logger.debug(f"Getting thumbnail for file: {file_id}")
        thumbnail = drive_service.get_thumbnail(file_id)
        return send_file(
            io.BytesIO(thumbnail),
            mimetype='image/jpeg'
        )
    except Exception as e:
        error_details = {
            'error': str(e),
            'traceback': traceback.format_exc(),
            'file_id': file_id
        }
        logger.error(f"Error getting thumbnail: {error_details}")
        return jsonify(error_details), 404

@app.route('/preview/<file_id>')
def get_preview(file_id):
    try:
        logger.debug(f"Getting preview for file: {file_id}")
        preview_link = drive_service.get_preview_link(file_id)
        return jsonify({
            'preview_url': preview_link,
            'debug_info': {
                'file_id': file_id
            }
        })
    except Exception as e:
        error_details = {
            'error': str(e),
            'traceback': traceback.format_exc(),
            'file_id': file_id
        }
        logger.error(f"Error getting preview: {error_details}")
        return jsonify(error_details), 500