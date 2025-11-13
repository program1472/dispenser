<div class="wrap">
<section id="sec-req" class="card">
    <div class="card-hd">
      <div><div class="card-ttl">수정요청</div><div class="card-sub">상태 변경 · 히스토리 타임라인 · CSV · 상세 팝업 · 업로드/다운로드</div></div>
      <div style="display:flex;gap:8px;align-items:center" class="no-print">
        <input id="qReq" class="input" placeholder="고객/요청 검색">
        <select id="fState" class="select"><option value="">전체 상태</option><option>OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option></select>
        <button id="btnReqCsv" class="btn">CSV</button>
      </div>
    </div>
    <div class="card-bd table-wrap">
      <table class="table" id="tblReq"><thead><tr><th>ID</th><th>고객</th><th>제목</th><th>요청일</th><th>마감일</th><th class="no-print">업로드</th><th>상태</th><th class="no-print">동작</th></tr></thead><tbody><tr>
      <td><a href="#" data-id="R003" class="req-link">R003</a></td>
      <td>고객2</td>
      <td>콘텐츠 수정 3</td>
      <td>2025-10-10</td>
      <td>2025-10-18</td>
      <td class="no-print"><input type="file" data-up="R003"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R003" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R003">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R007" class="req-link">R007</a></td>
      <td>고객4</td>
      <td>콘텐츠 수정 7</td>
      <td>2025-10-10</td>
      <td>2025-10-22</td>
      <td class="no-print"><input type="file" data-up="R007"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R007" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R007">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R010" class="req-link">R010</a></td>
      <td>고객5</td>
      <td>콘텐츠 수정 10</td>
      <td>2025-10-09</td>
      <td>2025-10-18</td>
      <td class="no-print"><input type="file" data-up="R010"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R010" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R010">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R022" class="req-link">R022</a></td>
      <td>고객11</td>
      <td>콘텐츠 수정 22</td>
      <td>2025-10-09</td>
      <td>2025-10-19</td>
      <td class="no-print"><input type="file" data-up="R022"></td>
      <td class="td-status" data-s="OPEN">
        <select data-state="R022" class="select">
          <option selected="">OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R022">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R005" class="req-link">R005</a></td>
      <td>고객3</td>
      <td>콘텐츠 수정 5</td>
      <td>2025-10-08</td>
      <td>2025-10-18</td>
      <td class="no-print"><input type="file" data-up="R005"></td>
      <td class="td-status" data-s="OPEN">
        <select data-state="R005" class="select">
          <option selected="">OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R005">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R018" class="req-link">R018</a></td>
      <td>고객9</td>
      <td>콘텐츠 수정 18</td>
      <td>2025-10-08</td>
      <td>2025-10-18</td>
      <td class="no-print"><input type="file" data-up="R018"></td>
      <td class="td-status" data-s="IN_PROGRESS">
        <select data-state="R018" class="select">
          <option>OPEN</option><option>보완요청</option><option selected="">IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R018">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R004" class="req-link">R004</a></td>
      <td>고객2</td>
      <td>콘텐츠 수정 4</td>
      <td>2025-10-03</td>
      <td>2025-10-13</td>
      <td class="no-print"><input type="file" data-up="R004"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R004" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R004">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R014" class="req-link">R014</a></td>
      <td>고객7</td>
      <td>콘텐츠 수정 14</td>
      <td>2025-10-03</td>
      <td>2025-10-15</td>
      <td class="no-print"><input type="file" data-up="R014"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R014" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R014">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R020" class="req-link">R020</a></td>
      <td>고객10</td>
      <td>콘텐츠 수정 20</td>
      <td>2025-10-03</td>
      <td>2025-10-11</td>
      <td class="no-print"><input type="file" data-up="R020"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R020" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R020">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R001" class="req-link">R001</a></td>
      <td>고객1</td>
      <td>콘텐츠 수정 1</td>
      <td>2025-10-02</td>
      <td>2025-10-12</td>
      <td class="no-print"><input type="file" data-up="R001"></td>
      <td class="td-status" data-s="OPEN">
        <select data-state="R001" class="select">
          <option selected="">OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R001">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R013" class="req-link">R013</a></td>
      <td>고객7</td>
      <td>콘텐츠 수정 13</td>
      <td>2025-10-01</td>
      <td>2025-10-11</td>
      <td class="no-print"><input type="file" data-up="R013"></td>
      <td class="td-status" data-s="IN_PROGRESS">
        <select data-state="R013" class="select">
          <option>OPEN</option><option>보완요청</option><option selected="">IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R013">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R016" class="req-link">R016</a></td>
      <td>고객8</td>
      <td>콘텐츠 수정 16</td>
      <td>2025-10-01</td>
      <td>2025-10-14</td>
      <td class="no-print"><input type="file" data-up="R016"></td>
      <td class="td-status" data-s="OPEN">
        <select data-state="R016" class="select">
          <option selected="">OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R016">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R015" class="req-link">R015</a></td>
      <td>고객8</td>
      <td>콘텐츠 수정 15</td>
      <td>2025-09-30</td>
      <td>2025-10-11</td>
      <td class="no-print"><input type="file" data-up="R015"></td>
      <td class="td-status" data-s="OPEN">
        <select data-state="R015" class="select">
          <option selected="">OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R015">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R019" class="req-link">R019</a></td>
      <td>고객10</td>
      <td>콘텐츠 수정 19</td>
      <td>2025-09-30</td>
      <td>2025-10-09</td>
      <td class="no-print"><input type="file" data-up="R019"></td>
      <td class="td-status" data-s="OPEN">
        <select data-state="R019" class="select">
          <option selected="">OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R019">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R021" class="req-link">R021</a></td>
      <td>고객11</td>
      <td>콘텐츠 수정 21</td>
      <td>2025-09-28</td>
      <td>2025-10-10</td>
      <td class="no-print"><input type="file" data-up="R021"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R021" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R021">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R008" class="req-link">R008</a></td>
      <td>고객4</td>
      <td>콘텐츠 수정 8</td>
      <td>2025-09-23</td>
      <td>2025-10-05</td>
      <td class="no-print"><input type="file" data-up="R008"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R008" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R008">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R009" class="req-link">R009</a></td>
      <td>고객5</td>
      <td>콘텐츠 수정 9</td>
      <td>2025-09-19</td>
      <td>2025-10-02</td>
      <td class="no-print"><input type="file" data-up="R009"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R009" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R009">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R017" class="req-link">R017</a></td>
      <td>고객9</td>
      <td>콘텐츠 수정 17</td>
      <td>2025-09-19</td>
      <td>2025-09-30</td>
      <td class="no-print"><input type="file" data-up="R017"></td>
      <td class="td-status" data-s="OPEN">
        <select data-state="R017" class="select">
          <option selected="">OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R017">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R012" class="req-link">R012</a></td>
      <td>고객6</td>
      <td>콘텐츠 수정 12</td>
      <td>2025-09-18</td>
      <td>2025-10-01</td>
      <td class="no-print"><input type="file" data-up="R012"></td>
      <td class="td-status" data-s="OPEN">
        <select data-state="R012" class="select">
          <option selected="">OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R012">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R002" class="req-link">R002</a></td>
      <td>고객1</td>
      <td>콘텐츠 수정 2</td>
      <td>2025-09-17</td>
      <td>2025-09-28</td>
      <td class="no-print"><input type="file" data-up="R002"></td>
      <td class="td-status" data-s="보완요청">
        <select data-state="R002" class="select">
          <option>OPEN</option><option selected="">보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R002">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R006" class="req-link">R006</a></td>
      <td>고객3</td>
      <td>콘텐츠 수정 6</td>
      <td>2025-09-17</td>
      <td>2025-09-28</td>
      <td class="no-print"><input type="file" data-up="R006"></td>
      <td class="td-status" data-s="IN_PROGRESS">
        <select data-state="R006" class="select">
          <option>OPEN</option><option>보완요청</option><option selected="">IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R006">CSV 다운로드</button></td></tr><tr>
      <td><a href="#" data-id="R011" class="req-link">R011</a></td>
      <td>고객6</td>
      <td>콘텐츠 수정 11</td>
      <td>2025-09-16</td>
      <td>2025-09-28</td>
      <td class="no-print"><input type="file" data-up="R011"></td>
      <td class="td-status" data-s="OPEN">
        <select data-state="R011" class="select">
          <option selected="">OPEN</option><option>보완요청</option><option>IN_PROGRESS</option><option>DONE</option>
        </select>
      </td>
      <td class="no-print"><button class="btn" data-dl="R011">CSV 다운로드</button></td></tr></tbody></table>
      <div class="small" style="margin-top:6px">※ 요청 클릭 시 상세 팝업. 상태값: OPEN → 보완요청 → IN_PROGRESS → DONE</div>
    </div>
  </section>
</div>