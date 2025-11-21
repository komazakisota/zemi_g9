/**
 * オートコンプリート機能
 */

class Autocomplete {
    constructor(inputElement, suggestions) {
        this.input = inputElement;
        this.suggestions = suggestions;
        this.currentFocus = -1;
        this.listId = `autocomplete-list-${Math.random().toString(36).substr(2, 9)}`;
        
        this.init();
    }
    
    init() {
        // 入力イベント
        this.input.addEventListener('input', () => this.onInput());
        
        // キーボードナビゲーション
        this.input.addEventListener('keydown', (e) => this.onKeyDown(e));
        
        // 外側クリックで閉じる
        document.addEventListener('click', (e) => {
            if (e.target !== this.input) {
                this.closeList();
            }
        });
    }
    
    onInput() {
        const value = this.input.value.toLowerCase();
        this.closeList();
        
        if (!value) return;
        
        // マッチする候補をフィルタリング
        const matches = this.suggestions.filter(suggestion => 
            suggestion.toLowerCase().includes(value)
        );
        
        if (matches.length === 0) return;
        
        // 候補リストを作成
        const list = document.createElement('div');
        list.setAttribute('id', this.listId);
        list.setAttribute('class', 'autocomplete-items');
        this.input.parentNode.appendChild(list);
        
        // 候補を表示（最大10件）
        matches.slice(0, 10).forEach((suggestion, index) => {
            const item = document.createElement('div');
            item.innerHTML = this.highlightMatch(suggestion, value);
            item.addEventListener('click', () => {
                this.input.value = suggestion;
                this.closeList();
                // 入力イベントを発火
                this.input.dispatchEvent(new Event('input'));
            });
            list.appendChild(item);
        });
    }
    
    highlightMatch(text, query) {
        const index = text.toLowerCase().indexOf(query);
        if (index === -1) return text;
        
        return text.substring(0, index) +
               '<strong>' + text.substring(index, index + query.length) + '</strong>' +
               text.substring(index + query.length);
    }
    
    onKeyDown(e) {
        const list = document.getElementById(this.listId);
        if (!list) return;
        
        const items = list.getElementsByTagName('div');
        
        if (e.keyCode === 40) { // Down
            e.preventDefault();
            this.currentFocus++;
            this.setActive(items);
        } else if (e.keyCode === 38) { // Up
            e.preventDefault();
            this.currentFocus--;
            this.setActive(items);
        } else if (e.keyCode === 13) { // Enter
            e.preventDefault();
            if (this.currentFocus > -1 && items[this.currentFocus]) {
                items[this.currentFocus].click();
            }
        } else if (e.keyCode === 27) { // Escape
            this.closeList();
        }
    }
    
    setActive(items) {
        if (!items) return;
        
        // 全てのactiveクラスを削除
        Array.from(items).forEach(item => item.classList.remove('autocomplete-active'));
        
        // 範囲チェック
        if (this.currentFocus >= items.length) this.currentFocus = 0;
        if (this.currentFocus < 0) this.currentFocus = items.length - 1;
        
        // アクティブクラスを追加
        items[this.currentFocus].classList.add('autocomplete-active');
    }
    
    closeList() {
        const list = document.getElementById(this.listId);
        if (list) list.remove();
        this.currentFocus = -1;
    }
    
    updateSuggestions(newSuggestions) {
        this.suggestions = newSuggestions;
    }
}

// グローバルに保持
let courseAutocomplete = null;
let assignmentAutocomplete = null;