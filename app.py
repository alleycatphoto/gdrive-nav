import os
import logging
from flask import Flask, render_template, request, jsonify, send_file
from drive_service import DriveService
import io

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
        files = drive_service.list_files(folder_id)
        breadcrumbs = drive_service.get_breadcrumbs(folder_id)
        return jsonify({
            'files': files,
            'breadcrumbs': breadcrumbs
        })
    except Exception as e:
        logger.error(f"Error listing files: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/download')
def download_file():
    try:
        file_id = request.args.get('file_id')
        file_name = request.args.get('file_name')

        if not file_id or not file_name:
            return jsonify({'error': 'Missing file_id or file_name'}), 400

        content = drive_service.download_file(file_id)
        return send_file(
            io.BytesIO(content),
            download_name=file_name,
            as_attachment=True
        )
    except Exception as e:
        logger.error(f"Error downloading file: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/thumbnail/<file_id>')
def get_thumbnail(file_id):
    try:
        thumbnail = drive_service.get_thumbnail(file_id)
        return send_file(
            io.BytesIO(thumbnail),
            mimetype='image/jpeg'
        )
    except Exception as e:
        logger.error(f"Error getting thumbnail: {str(e)}")
        return '', 404

@app.route('/preview/<file_id>')
def get_preview(file_id):
    try:
        preview_link = drive_service.get_preview_link(file_id)
        return jsonify({'preview_url': preview_link})
    except Exception as e:
        logger.error(f"Error getting preview: {str(e)}")
        return jsonify({'error': str(e)}), 500